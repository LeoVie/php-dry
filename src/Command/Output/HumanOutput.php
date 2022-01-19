<?php

declare(strict_types=1);

namespace App\Command\Output;

use App\Collection\MethodsCollection;
use App\Command\Output\Helper\OutputHelper;
use App\Model\Method\Method;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class HumanOutput implements OutputFormat
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
        $this->verboseOutputHelper->headline($headline);

        return $this;
    }

    public function single(string $line): self
    {
        $this->verboseOutputHelper->single($line);

        return $this;
    }

    /** @param string[] $items */
    public function listing(array $items): self
    {
        $this->verboseOutputHelper->listing($items);

        return $this;
    }

    public function methodsCollection(MethodsCollection $methodsCollection): self
    {
        $methods = $methodsCollection->getAll();

        return $this->listing(array_map(fn(Method $m) => $m->__toString(), $methods));
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

    public function detectionFinishedForType(string $type): self
    {
        $this->verboseOutputHelper
            ->info(\Safe\sprintf('Clone detection finished for type %s.', $type));

        return $this;
    }
}