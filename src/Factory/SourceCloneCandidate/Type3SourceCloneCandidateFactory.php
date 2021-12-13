<?php

declare(strict_types=1);

namespace App\Factory\SourceCloneCandidate;

use App\Configuration\Configuration;
use App\Exception\CollectionCannotBeEmpty;
use App\Merge\Type3SourceCloneCandidatesMerger;
use App\Model\SourceCloneCandidate\Type2SourceCloneCandidate;
use App\Model\SourceCloneCandidate\Type3SourceCloneCandidate;
use App\Util\ArrayUtil;
use LeoVie\PhpGrouper\Service\Grouper;

class Type3SourceCloneCandidateFactory
{
    public function __construct(
        private ArrayUtil                        $arrayUtil,
        private Type3SourceCloneCandidatesMerger $type3SourceCloneCandidatesMerger,
        private Grouper                          $grouper
    )
    {
    }

    /**
     * @param Type2SourceCloneCandidate[] $type2SourceCloneCandidates
     *
     * @return Type3SourceCloneCandidate[]
     * @throws CollectionCannotBeEmpty
     */
    public function createMultiple(array $type2SourceCloneCandidates, Configuration $configuration): array
    {
        $type2SourceCloneCandidateGroups = $this->grouper->groupByCallback(
            $type2SourceCloneCandidates,
            fn(Type2SourceCloneCandidate $a, Type2SourceCloneCandidate $b): bool => $this->longestCommonSubsequenceIsLongerThanConfiguredThreshold(
                $a, $b, $configuration
            )
        );

        $type2SCCGroupsWithoutSubsetGroups = $this->arrayUtil->removeEntriesThatAreSubsetsOfOtherEntries($type2SourceCloneCandidateGroups);

        $type3SourceCloneCandidates = array_map(
            fn(array $type2SCCs): array => array_map(
                fn(Type2SourceCloneCandidate $type2SCC): Type3SourceCloneCandidate => Type3SourceCloneCandidate::create(
                    [$type2SCC->getTokenSequence()],
                    $type2SCC->getMethodsCollection()
                ),
                $type2SCCs
            ),
            $type2SCCGroupsWithoutSubsetGroups
        );

        return $this->type3SourceCloneCandidatesMerger->merge($type3SourceCloneCandidates);
    }

    private function longestCommonSubsequenceIsLongerThanConfiguredThreshold(
        Type2SourceCloneCandidate $a,
        Type2SourceCloneCandidate $b,
        Configuration             $configuration
    ): bool
    {
        if ($a === $b) {
            return false;
        }

        return similar_text($a->identity(), $b->identity()) > $configuration->minSimilarTokens();
    }
}