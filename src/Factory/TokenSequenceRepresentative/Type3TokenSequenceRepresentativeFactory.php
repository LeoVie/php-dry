<?php

declare(strict_types=1);

namespace App\Factory\TokenSequenceRepresentative;

use App\Configuration\Configuration;
use App\Exception\CollectionCannotBeEmpty;
use App\Merge\Type3TokenSequenceRepresentativeMerger;
use App\Model\TokenSequenceRepresentative\Type2TokenSequenceRepresentative;
use App\Model\TokenSequenceRepresentative\Type3TokenSequenceRepresentative;
use App\TokenAnalyze\LongestCommonSubsequenceAnalyzer;
use App\Util\ArrayUtil;

class Type3TokenSequenceRepresentativeFactory
{
    public function __construct(
        private LongestCommonSubsequenceAnalyzer       $longestCommonSubsequenceAnalyzer,
        private ArrayUtil                              $arrayUtil,
        private Type3TokenSequenceRepresentativeMerger $type3TokenSequenceRepresentativeMerger
    )
    {
    }

    // TODO: Vergleich eigentlich nur innerhalb der Methodensignatur-Gruppen

    /**
     * @param Type2TokenSequenceRepresentative[] $type2TokenSequenceRepresentatives
     *
     * @return Type3TokenSequenceRepresentative[]
     * @throws CollectionCannotBeEmpty
     */
    public function createMultiple(array $type2TokenSequenceRepresentatives, Configuration $configuration): array
    {
        $groups = [];

        $toCompare = array_slice($type2TokenSequenceRepresentatives, 1);

        foreach ($type2TokenSequenceRepresentatives as $a) {
            if (!array_key_exists($a->identity(), $groups)) {
                $groups[$a->identity()] = [$a];
            }

            foreach ($toCompare as $b) {
                $longestCommonSubsequence = $this->longestCommonSubsequenceAnalyzer->find($a->getTokenSequence(), $b->getTokenSequence());

                if ($longestCommonSubsequence->length() >= $configuration->minSimilarTokens()) {
                    $groups[$a->identity()][] = $b;
                }
            }

            $toCompare = array_slice($toCompare, 1);
        }

        $type3TokenSequenceRepresentatives = [];
        foreach ($this->arrayUtil->removeEntriesThatAreSubsetsOfOtherEntries($groups) as $groupKey => $group) {
            $type3TokenSequenceRepresentatives[$groupKey] = array_map(
                fn(Type2TokenSequenceRepresentative $t2tsp): Type3TokenSequenceRepresentative => Type3TokenSequenceRepresentative::create(
                    [$t2tsp->getTokenSequence()],
                    $t2tsp->getMethodsCollection()
                ),
                $group
            );
        }

        return $this->type3TokenSequenceRepresentativeMerger->merge($type3TokenSequenceRepresentatives);
    }
}