<?php

declare(strict_types=1);

namespace App\Configuration;

// TODO: Replace with symfony config
class Configuration
{
    private function __construct(
        private string $directory,
        private int    $minSimilarTokens,
        private int    $countOfParamSetsForType4Clones,
        private string $htmlReportFile,
    )
    {
    }

    public static function create(
        string $directory,
        int    $minSimilarTokens,
        int    $countOfParamSetsForType4Clones,
        string $htmlReportFile,
    ): self
    {
        return new self($directory, $minSimilarTokens, $countOfParamSetsForType4Clones, $htmlReportFile);
    }

    public function directory(): string
    {
        return $this->directory;
    }

    public function minSimilarTokens(): int
    {
        return $this->minSimilarTokens;
    }

    public function countOfParamSetsForType4Clones(): int
    {
        return $this->countOfParamSetsForType4Clones;
    }

    public function htmlReportFile(): string
    {
        return $this->htmlReportFile;
    }
}