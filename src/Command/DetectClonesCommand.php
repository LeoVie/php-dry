<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Output\DetectClonesCommandOutput;
use App\Command\Output\Helper\VerboseOutputHelper;
use App\Configuration\Configuration;
use App\Exception\CollectionCannotBeEmpty;
use App\Exception\NoParamRequestForParamType;
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
    private const ARGUMENT_MIN_SIMILAR_TOKENS = 'minSimilarTokens';
    private const ARGUMENT_COUNT_OF_PARAM_SETS_FOR_TYPE4_CLONES = 'countOfParamSetsForType4Clones';
    protected static $defaultName = 'app:detect-clones';

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
                self::ARGUMENT_MIN_SIMILAR_TOKENS,
                InputArgument::OPTIONAL,
                'How many similar tokens should be in two fragments to treat them as clones.',
                3
            )->addArgument(
                self::ARGUMENT_COUNT_OF_PARAM_SETS_FOR_TYPE4_CLONES,
                InputArgument::OPTIONAL,
                'How many param sets should get generated for each method signature set (type 4 clone detection)?',
                10
            );
    }

    /**
     * @throws CollectionCannotBeEmpty
     * @throws FilesystemException
     * @throws NodeTypeNotConvertable
     * @throws StringsException
     * @throws NoParamRequestForParamType
     * @throws NoParamGeneratorFoundForParamRequest
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = StopwatchFactory::create();
        $stopwatch->start('detect-clones');

        $output = DetectClonesCommandOutput::create(
            VerboseOutputHelper::create($input, $output),
            $stopwatch
        );

        $configuration = $this->createConfiguration($input);

        $detected = $this->detectClonesService->detectInDirectory($configuration, $output);
        $clonesToReport = $this->ignoreClonesService->extractNonIgnoredClones($detected, $configuration);

        $sourceCloneMethodScoresMappings = [];
        if (empty($clonesToReport)) {
            $output->noClonesFound();
        } else {
            foreach ($clonesToReport as $clone) {
                $output
                    ->headline($clone->getType())
                    ->methodsCollection($clone->getMethodsCollection());


                $methodScoresMappings = [];
                foreach ($clone->getMethodsCollection()->getAll() as $method) {
                    if ($clone->getType() === SourceClone::TYPE_4) {
                        $methodModifiedToNonClassContext = $this->methodModifierService->modifyMethodToNonClassContext(
                            $this->methodModifierService->buildMethod($method->getContent())
                        );
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

        $output->stopTime();

        return Command::SUCCESS;
    }

    private function createConfiguration(InputInterface $input): Configuration
    {
        return Configuration::create(
            $this->getStringArgument($input, self::ARGUMENT_DIRECTORY),
            $this->getIntArgument($input, self::ARGUMENT_MIN_SIMILAR_TOKENS),
            $this->getIntArgument($input, self::ARGUMENT_COUNT_OF_PARAM_SETS_FOR_TYPE4_CLONES),
            __DIR__ . '/../../report.html',
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
        /** @var int $value */
        $value = $input->getArgument($name);

        return $value;
    }
}