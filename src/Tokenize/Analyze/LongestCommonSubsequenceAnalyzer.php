<?php

declare(strict_types=1);

namespace App\Tokenize\Analyze;

use App\Tokenize\TokenSequence;

class LongestCommonSubsequenceAnalyzer
{
    public function find(TokenSequence $a, TokenSequence $b): int
    {
        if ($a->isEmpty() || $b->isEmpty()) {
            return 0;
        }

        if ($a->equals($b)) {
            return $a->length();
        }

        $table = [];
        for ($i = 0; $i <= $a->length(); $i++) {
            for ($j = 0; $j <= $b->length(); $j++) {
                $table[$i][$j] = $this->tableEntry($i, $j, $a, $b, $table);
            }
        }

        return $table[$a->length()][$b->length()];
    }

    /** @param int[][] $table */
    private function tableEntry(int $i, int $j, TokenSequence $a, TokenSequence $b, array $table): int
    {
        if ($i === 0 || $j === 0) {
            return 0;
        }

        if ($a->getTokens()[$i - 1] == $b->getTokens()[$j - 1]) {
            return 1 + $table[$i - 1][$j - 1];
        }

        return max(
            $table[$i][$j - 1],
            $table[$i - 1][$j]
        );
    }
}