<?php

declare(strict_types=1);

namespace App\Configuration;

// TODO: Replace with symfony config
class Configuration
{
    private function __construct(
        private string $directory,
        private int    $minSimilarTokensPercent,
        private int    $countOfParamSetsForType4Clones,
        private string $htmlReportFile,
        private int    $minTokenLength,
    )
    {
    }

    public static function create(
        string $directory,
        int    $minSimilarTokensPercent,
        int    $countOfParamSetsForType4Clones,
        string $htmlReportFile,
        int    $minTokenLength
    ): self
    {
        return new self($directory, $minSimilarTokensPercent, $countOfParamSetsForType4Clones, $htmlReportFile, $minTokenLength);
    }

    public function directory(): string
    {
        return $this->directory;
    }

    public function minSimilarTokensPercent(): int
    {
        return $this->minSimilarTokensPercent;
    }

    public function countOfParamSetsForType4Clones(): int
    {
        return $this->countOfParamSetsForType4Clones;
    }

    public function htmlReportFile(): string
    {
        return $this->htmlReportFile;
    }

    public function minTokenLength(): int
    {
        return $this->minTokenLength;
    }
}