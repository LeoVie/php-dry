<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Output\DetectClonesCommandOutput;
use App\Command\Output\Helper\VerboseOutputHelper;
use App\Configuration\Configuration;
use App\Model\Method\Method;
use App\Model\SourceClone\SourceClone;
use App\Service\DetectClonesService;
use App\ServiceFactory\StopwatchFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DetectClonesCommand extends Command
{
    protected static $defaultName = 'app:detect-clones';

    public function __construct(private DetectClonesService $detectClonesService)
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
                'minLines',
                InputArgument::OPTIONAL,
                'How many lines should a fragment be at least to be treated as a clone.',
                0
            )->addArgument(
                'countOfParamSets',
                InputArgument::OPTIONAL,
                'How many param sets should get generated for each method signature set (type 4 clone detection)?',
                10
            );
    }

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

        foreach ($detected as $clones) {
            foreach ($clones as $clone) {
                if ($this->cloneShouldBeIgnored($clone, $configuration)) {
                    continue;
                }

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
            (int)$input->getArgument('minLines'),
            (int)$input->getArgument('countOfParamSets'),
        );
    }

    private function cloneShouldBeIgnored(SourceClone $clone, Configuration $configuration): bool
    {
        $methodLines = array_map(fn(Method $m): int => $m->getCodePositionRange()->countOfLines(), $clone->getMethodsCollection()->getAll());
        if (empty($methodLines)) {
            return true;
        }

        return max($methodLines) < $configuration->minLines();
    }
}