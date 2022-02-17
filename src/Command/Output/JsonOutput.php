<?php

declare(strict_types=1);

namespace App\Command\Output;

use App\Collection\MethodsCollection;
use App\Command\Output\Helper\OutputHelper;
use App\Model\Method\Method;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class JsonOutput implements OutputFormat
{
    private function __construct
    (
        private OutputHelper $verboseOutputHelper
    )
    {
    }

    public static function create(OutputHelper $verboseOutputHelper, Stopwatch $stopwatch): self
    {
        return new self($verboseOutputHelper);
    }

    public function runtime(StopwatchEvent $runtime): void
    {
    }

    public function headline(string $headline): self
    {
        return $this;
    }

    public function single(string $line): self
    {
        $this->verboseOutputHelper->single($line);

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
        return $this;
    }

    public function foundMethods(int $methodsCount): self
    {
        return $this;
    }

    public function foundClones(string $clonesType, int $clonesCount): self
    {
        return $this;
    }

    public function lapTime(): self
    {
        return $this;
    }

    public function stopTime(): self
    {
        return $this;
    }

    public function noClonesFound(): self
    {
        return $this;
    }

    public function detectionRunningForType(string $type): self
    {
        return $this;
    }

    /** @inheritDoc */
    public function createProgressBarIterator(iterable $iterable): iterable
    {
        return $iterable;
    }

    public function sourceClones(array $sourceClones): self
    {
        return $this->single(json_encode($sourceClones));
    }
}