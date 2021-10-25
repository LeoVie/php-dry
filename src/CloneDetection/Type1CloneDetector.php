<?php

declare(strict_types=1);

namespace App\CloneDetection;

use App\Model\SourceClone\SourceClone;
use App\Model\TokenSequenceRepresentative\Type1TokenSequenceRepresentative;

class Type1CloneDetector
{
    public function __construct(private CloneDetector $cloneDetector)
    {
    }

    /**
     * @param Type1TokenSequenceRepresentative[] $type1TokenSequenceRepresentatives
     *
     * @return SourceClone[]
     */
    public function detect(array $type1TokenSequenceRepresentatives): array
    {
        return $this->cloneDetector->detect($type1TokenSequenceRepresentatives, SourceClone::TYPE_1);
    }
}