<?php

declare(strict_types=1);

namespace App\Util\Subsequence;

class SimilarTextSubsequenceUtil implements SubsequenceUtil
{
    public function percentageOfSimilarText(string $a, string $b): int
    {
        return (int)round((similar_text($a, $b) / (max(strlen($a), strlen($b)))) * 100);
    }
}