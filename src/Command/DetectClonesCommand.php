<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Output\DetectClonesCommandOutput;
use App\Command\Output\Helper\VerboseOutputHelper;
use App\Configuration\Configuration;
use App\Exception\CollectionCannotBeEmpty;
use App\Model\MethodScoresMapping;
use App\Model\SourceClone\SourceClone;
use App\Model\SourceCloneMethodScoresMapping;
use App\Output\HtmlOutput;
use App\Report\CommandLineReportBuilder;
use App\Report\JsonReportBuilder;
use App\Service\DetectClonesService;
use App\Service\IgnoreClonesService;
use App\ServiceFactory\StopwatchFactory;
use LeoVie\PhpCleanCode\Rule\FileRuleResults;
use LeoVie\PhpCleanCode\Service\CleanCodeCheckerService;
use LeoVie\PhpCleanCode\Service\CleanCodeScorerService;
use LeoVie\PhpFilesystem\Exception\InvalidBoundaries;
use LeoVie\PhpMethodModifier\Exception\MethodCannotBeModifiedToNonClassContext;
use LeoVie\PhpMethodModifier\Service\MethodModifierService;
use LeoVie\PhpMethodsParser\Exception\NodeTypeNotConvertable;
use LeoVie\PhpParamGenerator\Exception\NoParamGeneratorFoundForParamRequest;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\StringsException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DetectClonesCommand extends Command
{
    private const ARGUMENT_DIRECTORY = 'directory';
    private const OPTION_MIN_SIMILAR_TOKENS_PERCENTAGE = 'min-similar-tokens-percentage';
    private const OPTION_COUNT_OF_PARAM_SETS_FOR_TYPE4_CLONES = 'count-of-param-sets-for-type4-clones';
    private const OPTION_HTML_REPORT_FILEPATH = 'html-report-filepath';
    private const OPTION_MIN_TOKEN_LENGTH = 'min-token-length';
    private const OPTION_ENABLE_CONSTRUCT_NORMALIZATION = 'enable-construct-normalization';
    private const OPTION_ENABLE_LCS_ALGORITHM = 'enable-lcs-algorithm';
    private const OPTION_REPORT_FORMAT = 'report-format';
    private const OPTION_REPORTS_DIRECTORY = 'reports-directory';
    private const OPTION_SILENT = 'silent';
    protected static $defaultName = 'php-dry:check';

    public function __construct(
        private DetectClonesService       $detectClonesService,
        private IgnoreClonesService       $ignoreClonesService,
        private CleanCodeCheckerService   $cleanCodeCheckerService,
        private CleanCodeScorerService    $cleanCodeScorerService,
        private MethodModifierService     $methodModifierService,
        private HtmlOutput                $htmlOutput,
        private JsonReportBuilder         $jsonReportBuilder,
        private CommandLineReportBuilder  $commandLineReportBuilder,
        private DetectClonesCommandOutput $detectClonesCommandOutput,
    )
    {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                self::ARGUMENT_DIRECTORY,
                InputArgument::REQUIRED,
                'Absolute path of directory in which clones should get detected.'
            )->addOption(
                self::OPTION_REPORT_FORMAT,
                null,
                InputArgument::OPTIONAL,
                'Select report format [cli, json, html]',
                'cli'
            )->addOption(
                self::OPTION_HTML_REPORT_FILEPATH,
                null,
                InputArgument::OPTIONAL,
                'Absolute path of report html file.'
            )->addOption(
                self::OPTION_MIN_SIMILAR_TOKENS_PERCENTAGE,
                null,
                InputArgument::OPTIONAL,
                'How many similar tokens should be in two fragments to treat them as clones.',
                80
            )->addOption(
                self::OPTION_COUNT_OF_PARAM_SETS_FOR_TYPE4_CLONES,
                null,
                InputArgument::OPTIONAL,
                'How many param sets should get generated for each method signature set (type 4 clone detection)?',
                10
            )->addOption(
                self::OPTION_MIN_TOKEN_LENGTH,
                null,
                InputArgument::OPTIONAL,
                'How many tokens should be each clone long at least?',
                50
            )->addOption(
                self::OPTION_ENABLE_CONSTRUCT_NORMALIZATION,
                null,
                InputArgument::OPTIONAL,
                'Detect clones by normalizing language constructs?',
                false
            )->addOption(
                self::OPTION_ENABLE_LCS_ALGORITHM,
                null,
                InputArgument::OPTIONAL,
                'Use the LCS algorithm which is slow, but precise?',
                false
            )->addOption(
                self::OPTION_SILENT,
                null,
                InputArgument::OPTIONAL,
                'Should the command be silent?',
                false
            )->addOption(
                self::OPTION_REPORTS_DIRECTORY,
                null,
                InputArgument::OPTIONAL,
                'Select path for json / html report',
                __DIR__
            );
    }

    /**
     * @throws CollectionCannotBeEmpty
     * @throws FilesystemException
     * @throws NodeTypeNotConvertable
     * @throws StringsException
     * @throws NoParamGeneratorFoundForParamRequest
     * @throws InvalidBoundaries
     * @throws MethodCannotBeModifiedToNonClassContext
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = StopwatchFactory::create();
        $stopwatch->start('detect-clones');

        $commandOutput = $this->detectClonesCommandOutput
            ->setOutputHelper(VerboseOutputHelper::create($input, $output))
            ->setStopwatch($stopwatch);

        $configuration = $this->createConfiguration($input);

        $detected = $this->detectClonesService->detectInDirectory($configuration, $commandOutput);
        $clonesToReport = $this->ignoreClonesService->extractNonIgnoredClones($detected, $configuration);

        $sourceCloneMethodScoresMappings = [];
        if (empty($clonesToReport)) {
            $commandOutput->noClonesFound()
                ->stopTime();

            return Command::SUCCESS;
        } else {
            $commandOutput->newLine(2);
            $jsonReport = $this->jsonReportBuilder->build($clonesToReport);

            if ($this->getStringOption($input, self::OPTION_REPORT_FORMAT) === 'json') {
                \Safe\file_put_contents(
                    $this->getStringOption($input, self::OPTION_REPORTS_DIRECTORY) . '/' . 'php-dry.json',
                    $jsonReport
                );
            } else if ($this->getStringOption($input, self::OPTION_REPORT_FORMAT) === 'cli') {
                $output->write($this->commandLineReportBuilder->build($jsonReport));
            }

            foreach ($clonesToReport as $clone) {
                $methodScoresMappings = [];
                foreach ($clone->getMethodsCollection()->getAll() as $method) {
                    if ($clone->getType() === SourceClone::TYPE_4) {
                        try {
                            $methodModifiedToNonClassContext = $this->methodModifierService->modifyMethodToNonClassContext(
                                $this->methodModifierService->buildMethod($method->getContent())
                            );
                        } catch (MethodCannotBeModifiedToNonClassContext) {
                            continue;
                        }
                        $methodContent = $methodModifiedToNonClassContext->getCode();

                        $ruleResults = FileRuleResults::create($method->getFilepath(), $this->cleanCodeCheckerService->checkCode('<?php ' . $methodContent));

                        $scoresResult = $this->cleanCodeScorerService->createScoresResult($ruleResults);

                        $methodScoresMappings[] = MethodScoresMapping::create(
                            $method,
                            $scoresResult->getScores()
                        );
                    } else {
                        $methodScoresMappings[] = MethodScoresMapping::create(
                            $method,
                            []
                        );
                    }
                }

                $sourceCloneMethodScoresMappings[] = SourceCloneMethodScoresMapping::create(
                    $clone,
                    $methodScoresMappings
                );
            }
        }

        $this->htmlOutput->createReport($sourceCloneMethodScoresMappings, $configuration);

        $commandOutput->stopTime();

        return Command::FAILURE;
    }

    private function createConfiguration(InputInterface $input): Configuration
    {
        return Configuration::create(
            $this->getStringArgument($input, self::ARGUMENT_DIRECTORY),
            $this->getIntOption($input, self::OPTION_MIN_SIMILAR_TOKENS_PERCENTAGE),
            $this->getIntOption($input, self::OPTION_COUNT_OF_PARAM_SETS_FOR_TYPE4_CLONES),
            $this->getStringOption($input, self::OPTION_HTML_REPORT_FILEPATH),
            $this->getIntOption($input, self::OPTION_MIN_TOKEN_LENGTH),
            $this->getBoolOption($input, self::OPTION_ENABLE_CONSTRUCT_NORMALIZATION),
            $this->getBoolOption($input, self::OPTION_ENABLE_LCS_ALGORITHM),
        );
    }

    private function getStringArgument(InputInterface $input, string $name): string
    {
        /** @var string $value */
        $value = $input->getArgument($name);

        return $value;
    }

    private function getStringOption(InputInterface $input, string $name): string
    {
        /** @var string $value */
        $value = $input->getOption($name);

        return $value;
    }

    private function getIntOption(InputInterface $input, string $name): int
    {
        /** @var int $value */
        $value = $input->getOption($name);

        return $value;
    }

    private function getBoolOption(InputInterface $input, string $name): bool
    {
        /** @var bool $value */
        $value = $input->getOption($name) === 'true';

        return $value;
    }
}