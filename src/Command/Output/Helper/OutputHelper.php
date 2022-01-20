<?php

declare(strict_types=1);

namespace App\Command\Output\Helper;

use App\ServiceFactory\SymfonyStyleFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OutputHelper
{
    private SymfonyStyle $io;

    final private function __construct(InputInterface $input, OutputInterface $output, private int $verbosityLevel)
    {
        $this->io = SymfonyStyleFactory::create($input, $output);
        $this->io->setVerbosity($this->verbosityLevel);
    }

    public static function create(InputInterface $input, OutputInterface $output, int $verbosityLevel): static
    {
        return new static($input, $output, $verbosityLevel);
    }

    public function headline(string $headline, int $level = 0): self
    {
        $this->io->section($this->formatLine($headline, $level));

        return $this;
    }

    /** @param string[] $items */
    public function listing(array $items, int $level = 0): self
    {
        $this->io->listing(array_map(fn(string $l) => $this->formatLine($l, $level), $items));

        return $this;
    }

    public function single(string $string, int $level = 0): self
    {
        $this->io->writeln($this->formatLine($string, $level));

        return $this;
    }

    private function formatLine(string $message, int $level = 0): string
    {
        return str_repeat("    ", $level) . $message;
    }

    public function emptyLine(int $count = 1): self
    {
        $this->io->newLine($count);

        return $this;
    }

    public function info(string $message): self
    {
        $this->io->info($message);

        return $this;
    }

    public function createProgressBarIterator(iterable $iterable): iterable
    {
        return $this->io->createProgressBar()->iterate($iterable);
    }
}