<?php

declare(strict_types=1);

namespace App\CloneDetection;

use App\Model\SourceClone\SourceClone;
use App\Model\SourceCloneCandidate\Type2SourceCloneCandidate;

class Type2CloneDetector
{
    public function __construct(private CloneDetector $cloneDetector)
    {
    }

    /**
     * @param Type2SourceCloneCandidate[] $type2SourceCloneCandidates
     *
     * @return SourceClone[]
     */
    public function detect(array $type2SourceCloneCandidates): array
    {
        return $this->cloneDetector->detect($type2SourceCloneCandidates, SourceClone::TYPE_2);
    }
}