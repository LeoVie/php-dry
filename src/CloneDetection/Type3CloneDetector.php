<?php

declare(strict_types=1);

namespace App\CloneDetection;

use App\Model\SourceClone\SourceClone;
use App\Model\TokenSequenceRepresentative\Type3TokenSequenceRepresentative;

class Type3CloneDetector
{
    public function __construct(private CloneDetector $cloneDetector)
    {
    }

    /**
     * @param Type3TokenSequenceRepresentative[] $type3TokenSequenceRepresentatives
     *
     * @return SourceClone[]
     */
    public function detect(array $type3TokenSequenceRepresentatives): array
    {
        return $this->cloneDetector->detect($type3TokenSequenceRepresentatives, SourceClone::TYPE_3);
    }
}