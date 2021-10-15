<?php

declare(strict_types=1);

namespace App\CloneDetection;

use App\Model\SourceClone\SourceClone;
use App\Model\TokenSequenceRepresentative\ExactTokenSequenceRepresentative;

class Type1CloneDetector
{
    public function __construct(private CloneDetector $cloneDetector)
    {
    }

    /**
     * @param ExactTokenSequenceRepresentative[] $tokenSequenceRepresentatives
     *
     * @return SourceClone[]
     */
    public function detect(array $tokenSequenceRepresentatives): array
    {
        return $this->cloneDetector->detect($tokenSequenceRepresentatives, SourceClone::TYPE_1);
    }
}