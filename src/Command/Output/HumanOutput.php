<?php

declare(strict_types=1);

namespace App\Command\Output;

use App\Command\Output\Helper\OutputHelper;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class HumanOutput implements OutputFormat
{
    private OutputHelper $outputHelper;
    private Stopwatch $stopwatch;

    public function setOutputHelper(OutputHelper $outputHelper): self
    {
        $this->outputHelper = $outputHelper;

        return $this;
    }

    public function setStopwatch(Stopwatch $stopwatch): self
    {
        $this->stopwatch = $stopwatch;

        return $this;
    }

    public function getName(): string
    {
        return 'human';
    }

    public function runtime(StopwatchEvent $runtime): void
    {
        $this->outputHelper
            ->headline('Run information:')
            ->single($runtime->__toString());
    }

    public function single(string $line): self
    {
        $this->outputHelper->single($line);

        return $this;
    }

    public function newLine(int $count = 1): self
    {
        $this->outputHelper->emptyLine($count);

        return $this;
    }

    /** @param string[] $items */
    public function listing(array $items): self
    {
        $this->outputHelper->listing($items);

        return $this;
    }

    public function foundFiles(int $filesCount): self
    {
        return $this
            ->single(\Safe\sprintf('Found %s files:', $filesCount))
            ->lapTime();
    }

    public function foundMethods(int $methodsCount): self
    {
        return $this
            ->single(\Safe\sprintf('Found %s methods:', $methodsCount))
            ->lapTime();
    }

    public function foundClones(string $clonesType, int $clonesCount): self
    {
        return $this
            ->single(\Safe\sprintf('Found %s %s clones:', $clonesCount, $clonesType))
            ->lapTime();
    }

    public function lapTime(): self
    {
        return $this->single($this->stopwatch->lap('detect-clones')->__toString());
    }

    public function stopTime(): self
    {
        return $this->single($this->stopwatch->stop('detect-clones')->__toString());
    }

    public function noClonesFound(): self
    {
        return $this->single('No clones found.');
    }

    public function detectionRunningForType(string $type): self
    {
        $this->outputHelper
            ->info(\Safe\sprintf('Detecting type %s clones', $type));

        return $this;
    }

    /** @inheritDoc */
    public function createProgressBarIterator(iterable $iterable): iterable
    {
        return $this->outputHelper->createProgressBarIterator($iterable);
    }
}