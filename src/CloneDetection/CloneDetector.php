<?php

declare(strict_types=1);

namespace App\CloneDetection;

use App\Model\SourceClone\SourceClone;
use App\Model\SourceCloneCandidate\SourceCloneCandidate;

class CloneDetector
{
    /**
     * @param SourceCloneCandidate[] $sourceCloneCandidates
     *
     * @return SourceClone[]
     */
    public function detect(array $sourceCloneCandidates, string $type): array
    {
        return array_map(
            fn(SourceCloneCandidate $scc): SourceClone => SourceClone::create($type, $scc->getMethodsCollection()),
            array_filter(
                $sourceCloneCandidates,
                fn(SourceCloneCandidate $sc): bool => $sc->getMethodsCollection()->count() > 1
            )
        );
    }
}