<?php

declare(strict_types=1);

namespace App\TokenAnalyze;

use Eloquent\Lcs\LcsSolver;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use PhpToken;

class LongestCommonSubsequenceAnalyzer
{
    public function __construct(private LcsSolver $lcsSolver)
    {
    }

    public function find(TokenSequence $a, TokenSequence $b): TokenSequence
    {
        /** @var PhpToken[] $longestCommonSubsequence */
        $longestCommonSubsequence = $this->lcsSolver->longestCommonSubsequence($a->getTokens(), $b->getTokens());

        return TokenSequence::create($longestCommonSubsequence);
    }
}