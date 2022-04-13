<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Output\DetectClonesCommandOutput;
use App\Command\Output\Helper\VerboseOutputHelper;
use App\Configuration\Configuration;
use App\Configuration\ConfigurationFactory;
use App\Exception\CollectionCannotBeEmpty;
use App\Exception\SubsequenceUtilNotFound;
use App\Model\MethodScoresMapping;
use App\Model\SourceClone\SourceClone;
use App\Model\SourceCloneMethodScoresMapping;
use App\Report\Formatter\CliReportFormatter;
use App\Report\Formatter\HtmlReportFormatter;
use App\Report\Formatter\JsonReportFormatter;
use App\Report\ReportBuilder;
use App\Report\Saver\CliReportReporter;
use App\Report\Saver\HtmlReportReporter;
use App\Report\Saver\JsonReportReporter;
use App\Service\DetectClonesService;
use App\Service\IgnoreClonesService;
use App\ServiceFactory\StopwatchFactory;
use LeoVie\PhpCleanCode\Rule\FileRuleResults;
use LeoVie\PhpCleanCode\Service\CleanCodeCheckerService;
use LeoVie\PhpCleanCode\Service\CleanCodeScorerService;
use LeoVie\PhpMethodModifier\Exception\MethodCannotBeModifiedToNonClassContext;
use LeoVie\PhpMethodModifier\Service\MethodModifierService;
use LeoVie\PhpParamGenerator\Exception\NoParamGeneratorFoundForParamRequest;
use Safe\Exceptions\FilesystemException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DetectClonesCommand extends Command
{
    public const NAME = 'php-dry:check';
    public const ARGUMENT_DIRECTORY = 'directory';
    public const OPTION_CONFIG = 'config';
    protected static $defaultName = self::NAME;

    public function __construct(
        private ConfigurationFactory      $configurationFactory,
        private DetectClonesService       $detectClonesService,
        private IgnoreClonesService       $ignoreClonesService,
        private CleanCodeCheckerService   $cleanCodeCheckerService,
        private CleanCodeScorerService    $cleanCodeScorerService,
        private MethodModifierService     $methodModifierService,
        private HtmlReportFormatter       $htmlReportFormatter,
        private JsonReportFormatter       $jsonReportFormatter,
        private CliReportFormatter        $cliReportFormatter,
        private DetectClonesCommandOutput $detectClonesCommandOutput,
        private HtmlReportReporter        $htmlReportReporter,
        private JsonReportReporter        $jsonReportReporter,
        private CliReportReporter         $cliReportReporter,
        private ReportBuilder             $reportBuilder,
    )
    {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this->addArgument(
            self::ARGUMENT_DIRECTORY,
            InputArgument::REQUIRED,
            'Absolute path of directory in which clones should get detected.'
        )->addOption(
            self::OPTION_CONFIG,
            'c',
            InputOption::VALUE_REQUIRED,
            'Path to php-dry.xml',
        );
    }

    /**
     * @throws CollectionCannotBeEmpty
     * @throws FilesystemException
     * @throws NoParamGeneratorFoundForParamRequest
     * @throws MethodCannotBeModifiedToNonClassContext
     * @throws SubsequenceUtilNotFound
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

        $this->report($sourceCloneMethodScoresMappings, $configuration);

        $commandOutput->stopTime();

        return Command::FAILURE;
    }

    private function createConfiguration(InputInterface $input): Configuration
    {
        $configuration = $this->configurationFactory->createConfigurationFromXmlFile(
            $this->getStringOption($input, self::OPTION_CONFIG)
        );

        $configuration->setDirectory($this->getStringArgument($input, self::ARGUMENT_DIRECTORY));

        return $configuration;
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

    /** @param array<SourceCloneMethodScoresMapping> $sourceCloneMethodScoresMappings */
    private function report(array $sourceCloneMethodScoresMappings, Configuration $configuration): void
    {
        $report = $this->reportBuilder->createReport($sourceCloneMethodScoresMappings, $configuration);

        if ($configuration->getReportConfiguration()->getHtml()) {
            $htmlReport = $this->htmlReportFormatter->format($report);

            $this->htmlReportReporter->report($htmlReport, $configuration);
        }
        if ($configuration->getReportConfiguration()->getJson()) {
            $jsonReport = $this->jsonReportFormatter->format($report);

            $this->jsonReportReporter->report($jsonReport, $configuration);
        }
        if ($configuration->getReportConfiguration()->getCli()) {
            $cliReport = $this->cliReportFormatter->format($report);

            $this->cliReportReporter->report($cliReport, $configuration);
        }
    }
}
