<?php

declare(strict_types=1);

namespace App\Merge;

use App\Collection\MethodsCollection;
use App\Exception\CollectionCannotBeEmpty;
use App\Model\SourceCloneCandidate\Type2SourceCloneCandidate;

class Type2SourceCloneCandidatesMerger
{
    /**
     * @param Type2SourceCloneCandidate[] $type2SourceCloneCandidates
     *
     * @return Type2SourceCloneCandidate[]
     * @throws CollectionCannotBeEmpty
     */
    public function merge(array $type2SourceCloneCandidates): array
    {
        /** @var Type2SourceCloneCandidate[] $mergedType2SourceCloneCandidates */
        $mergedType2SourceCloneCandidates = [];

        foreach ($type2SourceCloneCandidates as $type2SourceCloneCandidate) {
            $mergedType2SourceCloneCandidates = $this->addToExistingType2SourceCloneCandidateOrCreateNewOne(
                $type2SourceCloneCandidate,
                $mergedType2SourceCloneCandidates
            );
        }

        return $mergedType2SourceCloneCandidates;
    }

    /**
     * @param Type2SourceCloneCandidate $type2SourceCloneCandidate
     * @param Type2SourceCloneCandidate[] $newType2SourceCloneCandidates
     * @return Type2SourceCloneCandidate[]
     * @throws CollectionCannotBeEmpty
     */
    private function addToExistingType2SourceCloneCandidateOrCreateNewOne(
        Type2SourceCloneCandidate $type2SourceCloneCandidate,
        array                     $newType2SourceCloneCandidates,
    ): array
    {
        $identity = $type2SourceCloneCandidate->identity();

        $newNormalizedMethods = $type2SourceCloneCandidate->getMethodsCollection()->getAll();
        if (!array_key_exists($identity, $newType2SourceCloneCandidates)) {
            $newType2SourceCloneCandidates[$identity] = Type2SourceCloneCandidate::create(
                $type2SourceCloneCandidate->getTokenSequence(),
                MethodsCollection::create(...$newNormalizedMethods),
            );

            return $newType2SourceCloneCandidates;
        }

        foreach ($newNormalizedMethods as $newNormalizedMethod) {
            $newType2SourceCloneCandidates[$identity]->getMethodsCollection()->add($newNormalizedMethod);
        }

        return $newType2SourceCloneCandidates;
    }
}