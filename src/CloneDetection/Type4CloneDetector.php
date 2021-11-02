<?php

declare(strict_types=1);

namespace App\CloneDetection;

use App\Model\SourceClone\SourceClone;
use App\Model\SourceCloneCandidate\Type4SourceCloneCandidate;

class Type4CloneDetector
{
    public function __construct(private CloneDetector $cloneDetector)
    {
    }

    /**
     * @param Type4SourceCloneCandidate[] $type4SourceCloneCandidates
     *
     * @return SourceClone[]
     */
    public function detect(array $type4SourceCloneCandidates): array
    {
        return $this->cloneDetector->detect($type4SourceCloneCandidates, SourceClone::TYPE_4);
    }
}