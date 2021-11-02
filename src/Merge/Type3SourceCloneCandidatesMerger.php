<?php

declare(strict_types=1);

namespace App\Merge;

use App\Collection\MethodsCollection;
use App\Exception\CollectionCannotBeEmpty;
use App\Model\SourceCloneCandidate\Type3SourceCloneCandidate;

class Type3SourceCloneCandidatesMerger
{
    /**
     * @param array<Type3SourceCloneCandidate[]> $groups
     * @return Type3SourceCloneCandidate[]
     *
     * @throws CollectionCannotBeEmpty
     */
    public function merge(array $groups): array
    {
        $mergedType3SourceCloneCandidates = [];
        foreach ($groups as $type3SourceCloneCandidates) {
            $tokenSequences = [];
            $methods = [];
            foreach ($type3SourceCloneCandidates as $type3SourceCloneCandidate) {
                $tokenSequences = array_merge(
                    $tokenSequences,
                    $type3SourceCloneCandidate->getTokenSequences()
                );
                $methods = array_merge($methods, $type3SourceCloneCandidate->getMethodsCollection()->getAll());
            }

            $mergedType3SourceCloneCandidates[] = Type3SourceCloneCandidate::create(
                $tokenSequences,
                MethodsCollection::create(...$methods)
            );
        }

        return $mergedType3SourceCloneCandidates;
    }
}