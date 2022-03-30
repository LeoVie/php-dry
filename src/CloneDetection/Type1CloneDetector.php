<?php

declare(strict_types=1);

namespace App\CloneDetection;

use App\Model\SourceClone\SourceClone;
use App\Model\SourceCloneCandidate\Type1SourceCloneCandidate;

class Type1CloneDetector
{
    public function __construct(private CloneDetector $cloneDetector)
    {
    }

    /**
     * @param Type1SourceCloneCandidate[] $type1SourceCloneCandidates
     *
     * @return SourceClone[]
     */
    public function detect(array $type1SourceCloneCandidates): array
    {
        return $this->cloneDetector->detect($type1SourceCloneCandidates, SourceClone::TYPE_1);
    }
}
