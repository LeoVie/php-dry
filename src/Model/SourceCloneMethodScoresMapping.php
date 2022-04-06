<?php

namespace App\Model;

use App\Model\SourceClone\SourceClone;

class SourceCloneMethodScoresMapping
{
    /** @param MethodScoresMapping[] $methodScoresMappings */
    private function __construct(
        private SourceClone $sourceClone,
        private array       $methodScoresMappings,
    ) {
    }

    /** @param MethodScoresMapping[] $methodScoresMappings */
    public static function create(SourceClone $sourceClone, array $methodScoresMappings): self
    {
        return new self($sourceClone, $methodScoresMappings);
    }

    public function getSourceClone(): SourceClone
    {
        return $this->sourceClone;
    }

    /** @return MethodScoresMapping[] */
    public function getMethodScoresMappings(): array
    {
        return $this->methodScoresMappings;
    }
}
