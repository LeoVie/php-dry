<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Output\Helper\VerboseOutputHelper;
use App\Command\Output\HumanOutput;
use App\Command\Output\OutputFormat;
use App\Command\Output\QuietOutput;
use App\Configuration\Configuration;
use App\Exception\CollectionCannotBeEmpty;
use App\Model\MethodScoresMapping;
use App\Model\SourceClone\SourceClone;
use App\Model\SourceCloneMethodScoresMapping;
use App\Output\HtmlOutput;
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
use Symfony\Component\Stopwatch\Stopwatch;

class DetectClonesCommand extends Command
{
    private const ARGUMENT_DIRECTORY = 'directory';
    private const ARGUMENT_MIN_SIMILAR_TOKENS_PERCENT = 'minSimilarTokensPercent';
    private const ARGUMENT_COUNT_OF_PARAM_SETS_FOR_TYPE4_CLONES = 'countOfParamSetsForType4Clones';
    private const ARGUMENT_HTML_REPORT_FILEPATH = 'htmlReportFilepath';
    private const ARGUMENT_MIN_TOKEN_LENGTH = 'minTokenLength';
    private const OPTION_SILENT_LONG = 'silent';
    private const OPTION_SILENT_SHORT = 's';
    protected static $defaultName = 'php-cd:check';

    public function __construct(
        private DetectClonesService     $detectClonesService,
        private IgnoreClonesService     $ignoreClonesService,
        private CleanCodeCheckerService $cleanCodeCheckerService,
        private CleanCodeScorerService  $cleanCodeScorerService,
        private MethodModifierService   $methodModifierService,
        private HtmlOutput              $htmlOutput
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
            )->addArgument(
                self::ARGUMENT_HTML_REPORT_FILEPATH,
                InputArgument::OPTIONAL,
                'Absolute path of report html file.',
                __DIR__ . '/../../report.html',
            )->addArgument(
                self::ARGUMENT_MIN_SIMILAR_TOKENS_PERCENT,
                InputArgument::OPTIONAL,
                'How many similar tokens should be in two fragments to treat them as clones.',
                80
            )->addArgument(
                self::ARGUMENT_COUNT_OF_PARAM_SETS_FOR_TYPE4_CLONES,
                InputArgument::OPTIONAL,
                'How many param sets should get generated for each method signature set (type 4 clone detection)?',
                10
            )->addOption(
                self::OPTION_SILENT_LONG,
                self::OPTION_SILENT_SHORT,
                InputArgument::OPTIONAL,
                'Should the command be silent?',
                false
            )->addArgument(
                self::ARGUMENT_MIN_TOKEN_LENGTH,
                InputArgument::OPTIONAL,
                'How many tokens should be each clone long at least?',
                50
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

        $commandOutput = $this->getOutputFormat($input, $output, $stopwatch);

        $configuration = $this->createConfiguration($input);

        $detected = $this->detectClonesService->detectInDirectory($configuration, $commandOutput);
        $clonesToReport = $this->ignoreClonesService->extractNonIgnoredClones($detected, $configuration);

        $sourceCloneMethodScoresMappings = [];
        if (empty($clonesToReport)) {
            $commandOutput->noClonesFound()
                ->stopTime();

            return Command::SUCCESS;
        } else {
            foreach ($clonesToReport as $clone) {
                $commandOutput
                    ->headline($clone->getType())
                    ->methodsCollection($clone->getMethodsCollection());


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

        $this->htmlOutput->createReport($sourceCloneMethodScoresMappings, $configuration->htmlReportFile());

        $commandOutput->stopTime();

        return Command::FAILURE;
    }

    private function getOutputFormat(InputInterface $input, OutputInterface $output, Stopwatch $stopwatch): OutputFormat
    {
        if ($this->getBoolOption($input, self::OPTION_SILENT_LONG)) {
            return QuietOutput::create(
                VerboseOutputHelper::create($input, $output),
                $stopwatch
            );
        }

        return HumanOutput::create(
            VerboseOutputHelper::create($input, $output),
            $stopwatch
        );
    }

    private function createConfiguration(InputInterface $input): Configuration
    {
        return Configuration::create(
            $this->getStringArgument($input, self::ARGUMENT_DIRECTORY),
            $this->getIntArgument($input, self::ARGUMENT_MIN_SIMILAR_TOKENS_PERCENT),
            $this->getIntArgument($input, self::ARGUMENT_COUNT_OF_PARAM_SETS_FOR_TYPE4_CLONES),
            $this->getStringArgument($input, self::ARGUMENT_HTML_REPORT_FILEPATH),
            $this->getIntArgument($input, self::ARGUMENT_MIN_TOKEN_LENGTH),
        );
    }

    private function getStringArgument(InputInterface $input, string $name): string
    {
        /** @var string $value */
        $value = $input->getArgument($name);

        return $value;
    }

    private function getIntArgument(InputInterface $input, string $name): int
    {
        return (int)$input->getArgument($name);
    }

    private function getBoolOption(InputInterface $input, string $name): bool
    {
        /** @var bool $value */
        $value = $input->getOption($name) === 'true';

        return $value;
    }
}