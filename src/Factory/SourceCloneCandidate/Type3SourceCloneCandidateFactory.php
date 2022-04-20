<?php

declare(strict_types=1);

namespace App\Factory\SourceCloneCandidate;

use App\Configuration\Configuration;
use App\Exception\CollectionCannotBeEmpty;
use App\Exception\SubsequenceUtilNotFound;
use App\Merge\Type3SourceCloneCandidatesMerger;
use App\Model\SourceCloneCandidate\Type2SourceCloneCandidate;
use App\Model\SourceCloneCandidate\Type3SourceCloneCandidate;
use App\Util\ArrayUtil;
use App\Util\Subsequence\SubsequenceUtilPicker;
use LeoVie\PhpGrouper\Service\Grouper;

class Type3SourceCloneCandidateFactory
{
    public function __construct(
        private ArrayUtil                        $arrayUtil,
        private Type3SourceCloneCandidatesMerger $type3SourceCloneCandidatesMerger,
        private Grouper                          $grouper,
        private SubsequenceUtilPicker            $subsequenceUtilPicker,
    ) {
    }

    /**
     * @param iterable<Type2SourceCloneCandidate> $type2SourceCloneCandidates
     *
     * @return Type3SourceCloneCandidate[]
     * @throws CollectionCannotBeEmpty
     * @throws SubsequenceUtilNotFound
     */
    public function createMultiple(iterable $type2SourceCloneCandidates): array
    {
        $configuration = Configuration::instance();

        $subsequenceUtil = $this->subsequenceUtilPicker->pick(
            $configuration->isEnableLcsAlgorithm()
                ? SubsequenceUtilPicker::STRATEGY_LCS
                : SubsequenceUtilPicker::STRATEGY_SIMILAR_TEXT
        );

        /** @var array<array<Type2SourceCloneCandidate>> $type2SourceCloneCandidateGroups */
        $type2SourceCloneCandidateGroups = $this->grouper->groupByCallback(
            $type2SourceCloneCandidates,
            fn (Type2SourceCloneCandidate $a, Type2SourceCloneCandidate $b): bool => $a->identity() !== $b->identity()
                && $subsequenceUtil->percentageOfSimilarText(
                    $a->identity(),
                    $b->identity()
                ) > $configuration->getMinSimilarTokensPercentage()
        );

        return $this->createMultipleFromGroups($type2SourceCloneCandidateGroups);
    }

    /**
     * @param array<Type2SourceCloneCandidate[]> $type2SourceCloneCandidateGroups
     *
     * @return Type3SourceCloneCandidate[]
     *
     * @throws CollectionCannotBeEmpty
     */
    private function createMultipleFromGroups(array $type2SourceCloneCandidateGroups): array
    {
        $type2SCCGroupsWithoutSubsetGroups = $this->arrayUtil->removeEntriesThatAreSubsetsOfOtherEntries($type2SourceCloneCandidateGroups);

        $type3SourceCloneCandidates = array_map(
            fn (array $type2SCCs): array => array_map(
                fn (Type2SourceCloneCandidate $type2SCC): Type3SourceCloneCandidate => Type3SourceCloneCandidate::create(
                    [$type2SCC->getTokenSequence()],
                    $type2SCC->getMethodsCollection()
                ),
                $type2SCCs
            ),
            $type2SCCGroupsWithoutSubsetGroups
        );

        return $this->type3SourceCloneCandidatesMerger->merge($type3SourceCloneCandidates);
    }
}
