<?php

declare(strict_types=1);

namespace App\Configuration;

// TODO: Replace with symfony config
class Configuration
{
    private function __construct(
        private string $directory,
        private int    $minLinesForType1AndType2Clones,
        private int    $minSimilarTokensForType3Clones,
        private int    $countOfParamSetsForType4Clones,
    )
    {
    }

    public static function create(
        string $directory,
        int    $minLinesForType1AndType2Clones,
        int    $minSimilarTokensForType3Clones,
        int    $countOfParamSetsForType4Clones,
    ): self
    {
        return new self($directory, $minLinesForType1AndType2Clones, $minSimilarTokensForType3Clones, $countOfParamSetsForType4Clones);
    }

    public function directory(): string
    {
        return $this->directory;
    }

    public function minLinesForType1AndType2Clones(): int
    {
        return $this->minLinesForType1AndType2Clones;
    }

    public function minSimilarTokensForType3Clones(): int
    {
        return $this->minSimilarTokensForType3Clones;
    }

    public function countOfParamSetsForType4Clones(): int
    {
        return $this->countOfParamSetsForType4Clones;
    }
}