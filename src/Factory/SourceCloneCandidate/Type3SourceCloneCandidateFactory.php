<?php

declare(strict_types=1);

namespace App\Factory\SourceCloneCandidate;

use App\Configuration\Configuration;
use App\Exception\CollectionCannotBeEmpty;
use App\Merge\Type3SourceCloneCandidatesMerger;
use App\Model\Method\Method;
use App\Model\Method\MethodSignatureGroup;
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

        $similarText = similar_text($a->identity(), $b->identity());
        $percentageOfSimilarText = ($similarText / (max(strlen($a->identity()), strlen($b->identity())))) * 100;

        return $percentageOfSimilarText > $configuration->minSimilarTokensPercent();

        $lcs = $this->lcs($a->identity(), $b->identity());
        $percentageOfEqualTokens = ($lcs / (max(strlen($a->identity()), strlen($b->identity())))) * 100;

        $similarText = similar_text($a->identity(), $b->identity());
        $percentageOfSimilarText = ($similarText / (max(strlen($a->identity()), strlen($b->identity())))) * 100;

        $file = fopen(__DIR__ . '/foo.csv', 'a');
        fwrite($file, "$lcs;$similarText;$percentageOfEqualTokens;$percentageOfSimilarText\n");
        fclose($file);

        return $percentageOfEqualTokens > $configuration->minSimilarTokensPercent();
    }

    private function lcs(string $a, string $b): int
    {
        $aLength = strlen($a);
        $bLength = strlen($b);

        if ($aLength === 0 || $bLength === 0) {
            return 0;
        }

        $table = [];

        foreach ([0, 1] as $i) {
            for ($j = 0; $j <= $bLength; $j++) {
                $table[$i][$j] = 0;
            }
        }

        for ($i = 1; $i < $aLength + 1; $i++) {
            for ($j = 0; $j < $bLength + 1; $j++) {
                $table[0][$j] = $table[1][$j];
            }

            for ($j = 1; $j < $bLength + 1; $j++) {
                $table[1][$j] = match (true) {
                    $a[$i - 1] === $b[$j - 1] => $table[0][$j - 1] + 1,
                    default => max(
                        $table[0][$j],
                        $table[1][$j - 1]
                    )
                };
            }
        }

        return $table[1][$bLength];
    }
}