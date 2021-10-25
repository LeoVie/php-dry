<?php

declare(strict_types=1);

namespace App\CloneDetection;

use App\Model\SourceClone\SourceClone;
use App\Model\TokenSequenceRepresentative\Type2TokenSequenceRepresentative;

class Type2CloneDetector
{
    public function __construct(private CloneDetector $cloneDetector)
    {
    }

    /**
     * @param Type2TokenSequenceRepresentative[] $type2TokenSequenceRepresentatives
     *
     * @return SourceClone[]
     */
    public function detect(array $type2TokenSequenceRepresentatives): array
    {
        return $this->cloneDetector->detect($type2TokenSequenceRepresentatives, SourceClone::TYPE_2);
    }
}