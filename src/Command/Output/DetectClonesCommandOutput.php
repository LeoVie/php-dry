<?php

declare(strict_types=1);

namespace App\Command\Output;

use App\Command\Output\Helper\OutputHelper;
use Symfony\Component\Stopwatch\StopwatchEvent;

class DetectClonesCommandOutput
{
    private function __construct
    (
        private OutputHelper $verboseOutputHelper,
    )
    {
    }

    public static function create(OutputHelper $verboseOutputHelper): self
    {
        return new self($verboseOutputHelper);
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
}