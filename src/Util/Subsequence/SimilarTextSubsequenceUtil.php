<?php

declare(strict_types=1);

namespace App\Util\Subsequence;

class SimilarTextSubsequenceUtil implements SubsequenceUtil
{
    public function isOverThreshold(string $a, string $b, int $threshold): bool
    {
        if ($a === $b) {
            return false;
        }

        $similarText = similar_text($a, $b);
        $percentageOfSimilarText = ($similarText / (max(strlen($a), strlen($b)))) * 100;

        return $percentageOfSimilarText > $threshold;
    }
}