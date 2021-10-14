<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Output\DetectClonesCommandOutput;
use App\Command\Output\Helper\VerboseOutputHelper;
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
        $stopwatch->start(self::class);

        $output = DetectClonesCommandOutput::create(
            VerboseOutputHelper::create($input, $output),
        );

        /** @var string $directory */
        $directory = $input->getArgument('directory');

        $countOfParamSets = (int)$input->getArgument('countOfParamSets');

        $minLines = (int)$input->getArgument('minLines');

        $detected = $this->detectClonesService->detectInDirectory($directory, $countOfParamSets, $output);

        foreach ($detected as $cloneType => $clones) {
            $output->headline($cloneType);
            foreach ($clones as $clone) {
                if (!$this->shouldBeReported($clone, $minLines)) {
                    continue;
                }

                $output
                    ->single($clone->getType())
                    ->listing(array_map(fn(Method $m) => $m->__toString(), $clone->getMethodsCollection()->getAll()));
            }
        }

        $runtime = $stopwatch->stop(self::class);

        $output->runtime($runtime);

        return Command::SUCCESS;
    }

    private function shouldBeReported(SourceClone $clone, int $minLines): bool
    {
        $first = $clone->getMethodsCollection()->getFirst();

        if ($first === null) {
            return false;
        }

        $codePositionRange = $first->getCodePositionRange();
        $lines = $codePositionRange->getEnd()->getLine() - $codePositionRange->getStart()->getLine();

        return $lines > $minLines;
    }
}