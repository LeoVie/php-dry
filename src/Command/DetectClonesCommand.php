<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Output\DetectClonesCommandOutput;
use App\Command\Output\Helper\VerboseOutputHelper;
use App\Configuration\Configuration;
use App\Exception\CollectionCannotBeEmpty;
use App\Exception\NodeTypeNotConvertable;
use App\Model\Method\Method;
use App\Model\SourceClone\SourceClone;
use App\Service\DetectClonesService;
use App\Service\IgnoreClonesService;
use App\ServiceFactory\StopwatchFactory;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\StringsException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DetectClonesCommand extends Command
{
    protected static $defaultName = 'app:detect-clones';

    public function __construct(private DetectClonesService $detectClonesService, private IgnoreClonesService $ignoreClonesService)
    {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'directory',
                InputArgument::REQUIRED,
                'Absolute path of directory in which clones should get detected.'
            )->addArgument(
                'minLinesForType1AndType2Clones',
                InputArgument::OPTIONAL,
                'How many lines should a fragment be at least to be treated as a clone.',
                0
            )->addArgument(
                'minSimilarTokensForType3Clones',
                InputArgument::OPTIONAL,
                'How many same tokens should be in two fragments to treat them as a clone.',
                3
            )->addArgument(
                'countOfParamSetsForType4Clones',
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

        if (empty($clonesToReport)) {
            $output->noClonesFound();
        } else {
            foreach ($clonesToReport as $clone) {
                $output
                    ->headline($clone->getType())
                    ->methodsCollection($clone->getMethodsCollection());
            }
        }

        $output->stopTime();

        return Command::SUCCESS;
    }

    private function createConfiguration(InputInterface $input): Configuration
    {
        return Configuration::create(
            (string)$input->getArgument('directory'),
            (int)$input->getArgument('minLinesForType1AndType2Clones'),
            (int)$input->getArgument('minSimilarTokensForType3Clones'),
            (int)$input->getArgument('countOfParamSetsForType4Clones'),
        );
    }
}