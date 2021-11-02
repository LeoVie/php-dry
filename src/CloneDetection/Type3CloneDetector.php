<?php

declare(strict_types=1);

namespace App\CloneDetection;

use App\Model\SourceClone\SourceClone;
use App\Model\SourceCloneCandidate\Type3SourceCloneCandidate;

class Type3CloneDetector
{
    public function __construct(private CloneDetector $cloneDetector)
    {
    }

    /**
     * @param Type3SourceCloneCandidate[] $type3SourceCloneCandidates
     *
     * @return SourceClone[]
     */
    public function detect(array $type3SourceCloneCandidates): array
    {
        return $this->cloneDetector->detect($type3SourceCloneCandidates, SourceClone::TYPE_3);
    }
}