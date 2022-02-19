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
        return $this->createSourceClonesFromSourceCloneCandidates($type, $sourceCloneCandidates);
    }

    /**
     * @param SourceCloneCandidate[] $sourceCloneCandidates
     *
     * @return SourceClone[]
     */
    private function createSourceClonesFromSourceCloneCandidates(string $type, array $sourceCloneCandidates): array
    {
        return array_map(
            fn(SourceCloneCandidate $scc): SourceClone => SourceClone::create($type, $scc->getMethodsCollection()),
            $this->findSourceCloneCandidatesWithMultipleMethods($sourceCloneCandidates)
        );
    }

    /**
     * @param SourceCloneCandidate[] $sourceCloneCandidates
     *
     * @return SourceCloneCandidate[]
     */
    private function findSourceCloneCandidatesWithMultipleMethods(array $sourceCloneCandidates): array
    {
        return array_filter(
            $sourceCloneCandidates,
            fn(SourceCloneCandidate $sc): bool => $sc->getMethodsCollection()->count() > 1
        );
    }
}