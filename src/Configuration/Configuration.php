<?php

declare(strict_types=1);

namespace App\Configuration;

// TODO: Replace with symfony config
class Configuration
{
    private function __construct(
        private string $directory,
        private int    $minLines,
        private int    $countOfParamSets
    )
    {
    }

    public static function create(
        string $directory,
        int    $minLines,
        int    $countOfParamSets,
    ): self
    {
        return new self($directory, $minLines, $countOfParamSets);
    }

    public function directory(): string
    {
        return $this->directory;
    }

    public function minLines(): int
    {
        return $this->minLines;
    }

    public function countOfParamSets(): int
    {
        return $this->countOfParamSets;
    }
}