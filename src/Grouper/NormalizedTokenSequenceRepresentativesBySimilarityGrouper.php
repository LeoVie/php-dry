<?php

declare(strict_types=1);

namespace App\Grouper;

use App\Collection\MethodsCollection;
use App\Configuration\Configuration;
use App\Exception\CollectionCannotBeEmpty;
use App\Model\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentative;
use App\Model\TokenSequenceRepresentative\SimilarTokenSequencesRepresentative;
use App\Tokenize\Analyze\LongestCommonSubsequenceAnalyzer;
use App\Util\ArrayUtil;

class NormalizedTokenSequenceRepresentativesBySimilarityGrouper
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
     * @return SimilarTokenSequencesRepresentative[]
     */
    public function group(array $normalizedTokenSequenceRepresentatives, Configuration $configuration): array
    {
        $groups = [];

        $toCompare = array_slice($normalizedTokenSequenceRepresentatives, 1);

        foreach ($normalizedTokenSequenceRepresentatives as $a) {
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

        return $this->merge($this->arrayUtil->removeEntriesThatAreSubsetsOfOtherEntries($groups));
    }

    /**
     * @param array<NormalizedTokenSequenceRepresentative[]> $groups
     * @return SimilarTokenSequencesRepresentative[]
     *
     * @throws CollectionCannotBeEmpty
     */
    private function merge(array $groups): array
    {
        $result = [];
        foreach ($groups as $normalizedTokenSequenceRepresentatives) {
            $tokenSequences = [];
            $methods = [];
            foreach ($normalizedTokenSequenceRepresentatives as $normalizedTokenSequenceRepresentative) {
                $tokenSequences[] = $normalizedTokenSequenceRepresentative->getTokenSequence();
                $methods = array_merge($methods, $normalizedTokenSequenceRepresentative->getMethodsCollection()->getAll());
            }

            $result[] = SimilarTokenSequencesRepresentative::create(
                $tokenSequences,
                MethodsCollection::create(...$methods)
            );
        }

        return $result;
    }
}