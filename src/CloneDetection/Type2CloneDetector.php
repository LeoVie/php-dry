<?php

declare(strict_types=1);

namespace App\CloneDetection;

use App\Model\SourceClone\SourceClone;
use App\Model\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentative;

class Type2CloneDetector
{
    public function __construct(private CloneDetector $cloneDetector)
    {
    }

    /**
     * @param NormalizedTokenSequenceRepresentative[] $normalizedTokenSequenceRepresentatives
     *
     * @return SourceClone[]
     */
    public function detect(array $normalizedTokenSequenceRepresentatives): array
    {
        return $this->cloneDetector->detect($normalizedTokenSequenceRepresentatives, SourceClone::TYPE_2);
    }
}