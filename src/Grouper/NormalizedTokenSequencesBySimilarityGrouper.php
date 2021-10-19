<?php

declare(strict_types=1);

namespace App\Grouper;

use App\Model\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentative;
use App\Tokenize\Analyze\LongestCommonSubsequenceAnalyzer;
use App\Util\ArrayUtil;

class NormalizedTokenSequencesBySimilarityGrouper
{
    public function __construct(
        private LongestCommonSubsequenceAnalyzer $longestCommonSubsequenceAnalyzer,
        private ArrayUtil                        $arrayUtil,
    )
    {
    }

    // TODO: Vergleich eigentlich nur innerhalb der Methodensignatur-Gruppen

    /**
     * @param NormalizedTokenSequenceRepresentative[] $normalizedTokenSequenceRepresentatives
     *
     * @return array<NormalizedTokenSequenceRepresentative[]>
     */
    public function groupSimilarNormalizedTokenSequenceRepresentatives(array $normalizedTokenSequenceRepresentatives, int $minSimilarTokens): array
    {
        $groups = [];

        $toCompare = array_slice($normalizedTokenSequenceRepresentatives, 1);

        foreach ($normalizedTokenSequenceRepresentatives as $a) {
            if (!array_key_exists($a->identity(), $groups)) {
                $groups[$a->identity()] = [$a];
            }

            foreach ($toCompare as $b) {
                $longestCommonSubsequence = $this->longestCommonSubsequenceAnalyzer->find($a->getTokenSequence(), $b->getTokenSequence());

                if ($longestCommonSubsequence >= $minSimilarTokens) {
                    $groups[$a->identity()][] = $b;
                }
            }

            $toCompare = array_slice($toCompare, 1);
        }

        return $this->arrayUtil->removeEntriesThatAreSubsetsOfOtherEntries($groups);
    }
}