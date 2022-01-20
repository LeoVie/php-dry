<?php

declare(strict_types=1);

namespace App\Util\Subsequence;

class LongestCommonSubsequenceUtil implements SubsequenceUtil
{
    public function isOverThreshold(string $a, string $b, int $threshold): bool
    {
        if ($a === $b) {
            return false;
        }

        $lcs = $this->lcs($a, $b);
        $percentageOfEqualTokens = ($lcs / (max(strlen($a), strlen($b)))) * 100;

        return $percentageOfEqualTokens > $threshold;
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