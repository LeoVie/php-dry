<?php

declare(strict_types=1);

namespace App\Util\Subsequence;

use App\Exception\SubsequenceUtilNotFound;

class SubsequenceUtilPicker
{
    public const STRATEGY_LCS = 'LCS';
    public const STRATEGY_SIMILAR_TEXT = 'similar_text';

    public function __construct(
        private LongestCommonSubsequenceUtil $longestCommonSubsequenceUtil,
        private SimilarTextSubsequenceUtil   $similarTextSubsequenceUtil,
    )
    {
    }

    /** @throws SubsequenceUtilNotFound */
    public function pick(string $strategy): SubsequenceUtil
    {
        return match ($strategy) {
            self::STRATEGY_LCS => $this->longestCommonSubsequenceUtil,
            self::STRATEGY_SIMILAR_TEXT => $this->similarTextSubsequenceUtil,
            default => throw SubsequenceUtilNotFound::create($strategy)
        };
    }
}