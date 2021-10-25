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
use LeoVie\PhpGrouper\Service\Grouper;

class Type3TokenSequenceRepresentativeFactory
{
    public function __construct(
        private LongestCommonSubsequenceAnalyzer       $longestCommonSubsequenceAnalyzer,
        private ArrayUtil                              $arrayUtil,
        private Type3TokenSequenceRepresentativeMerger $type3TokenSequenceRepresentativeMerger,
        private Grouper                                $grouper
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
        $type2TokenSequenceRepresentativeGroups = $this->grouper->groupByCallback(
            $type2TokenSequenceRepresentatives,
            function (Type2TokenSequenceRepresentative $a, Type2TokenSequenceRepresentative $b) use ($configuration): bool {
                if ($a === $b) {
                    return false;
                }

                $longestCommonSubsequence = $this->longestCommonSubsequenceAnalyzer->find($a->getTokenSequence(), $b->getTokenSequence());

                return $longestCommonSubsequence->length() >= $configuration->minSimilarTokens();
            }
        );

        $type2TSRGroupsWithoutSubsetGroups = $this->arrayUtil->removeEntriesThatAreSubsetsOfOtherEntries($type2TokenSequenceRepresentativeGroups);

        $type3TokenSequenceRepresentatives = array_map(
            fn(array $type2TSRs): array => array_map(
                fn(Type2TokenSequenceRepresentative $type2TSR): Type3TokenSequenceRepresentative => Type3TokenSequenceRepresentative::create(
                    [$type2TSR->getTokenSequence()],
                    $type2TSR->getMethodsCollection()
                ),
                $type2TSRs
            ),
            $type2TSRGroupsWithoutSubsetGroups
        );

        return $this->type3TokenSequenceRepresentativeMerger->merge($type3TokenSequenceRepresentatives);
    }
}