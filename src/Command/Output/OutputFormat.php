<?php

namespace App\Command\Output;

use App\Collection\MethodsCollection;
use App\Command\Output\Helper\OutputHelper;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

interface OutputFormat
{
    public static function create(OutputHelper $verboseOutputHelper, Stopwatch $stopwatch): self;

    public function runtime(StopwatchEvent $runtime): void;

    public function headline(string $headline): self;

    public function single(string $line): self;

    public function newLine(int $count = 1): self;

    /** @param string[] $items */
    public function listing(array $items): self;

    public function methodsCollection(MethodsCollection $methodsCollection): self;

    public function foundFiles(int $filesCount): self;

    public function foundMethods(int $methodsCount): self;

    public function foundClones(string $clonesType, int $clonesCount): self;

    public function lapTime(): self;

    public function stopTime(): self;

    public function noClonesFound(): self;

    public function detectionRunningForType(string $type): self;

    /**
     * @template T
     * @param iterable<T> $iterable
     *
     * @return iterable<T>
     */
    public function createProgressBarIterator(iterable $iterable): iterable;
}