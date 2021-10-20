<?php

declare(strict_types=1);

namespace App\CloneDetection;

use App\Model\SourceClone\SourceClone;
use App\Model\TokenSequenceRepresentative\SimilarTokenSequencesRepresentative;

class Type3CloneDetector
{
    public function __construct(private CloneDetector $cloneDetector)
    {
    }

    /**
     * @param SimilarTokenSequencesRepresentative[] $similarTokenSequenceRepresentatives
     *
     * @return SourceClone[]
     */
    public function detect(array $similarTokenSequenceRepresentatives): array
    {
        return $this->cloneDetector->detect($similarTokenSequenceRepresentatives, SourceClone::TYPE_3);
    }
}