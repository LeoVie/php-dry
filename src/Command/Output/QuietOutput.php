<?php

declare(strict_types=1);

namespace App\Command\Output;

use App\Collection\MethodsCollection;
use App\Command\Output\Helper\OutputHelper;
use App\Model\Method\Method;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class QuietOutput implements OutputFormat
{
    private function __construct
    (
        private OutputHelper $verboseOutputHelper,
        private Stopwatch    $stopwatch
    )
    {
    }

    public static function create(OutputHelper $verboseOutputHelper, Stopwatch $stopwatch): self
    {
        return new self($verboseOutputHelper, $stopwatch);
    }

    public function runtime(StopwatchEvent $runtime): void
    {
        $this->verboseOutputHelper
            ->headline('Run information:')
            ->single($runtime->__toString());
    }

    public function headline(string $headline): self
    {
        return $this;
    }

    public function single(string $line): self
    {
        return $this;
    }

    public function newLine(int $count = 1): self
    {
        return $this;
    }

    /** @param string[] $items */
    public function listing(array $items): self
    {
        return $this;
    }

    public function methodsCollection(MethodsCollection $methodsCollection): self
    {
        return $this;
    }

    public function foundFiles(int $filesCount): self
    {
        return $this->lapTime();
    }

    public function foundMethods(int $methodsCount): self
    {
        return $this->lapTime();
    }

    public function foundClones(string $clonesType, int $clonesCount): self
    {
        return $this->lapTime();
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
        return $this;
    }

    public function detectionRunningForType(string $type): self
    {
        return $this;
    }

    public function createProgressBarIterator(iterable $iterable): iterable
    {
        return $iterable;
    }
}