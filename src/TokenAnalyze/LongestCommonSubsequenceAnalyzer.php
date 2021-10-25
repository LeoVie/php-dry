<?php

declare(strict_types=1);

namespace App\TokenAnalyze;

use Eloquent\Lcs\LcsSolver;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;

class LongestCommonSubsequenceAnalyzer
{
    public function __construct(private LcsSolver $lcsSolver)
    {
    }

    public function find(TokenSequence $a, TokenSequence $b): TokenSequence
    {
        return TokenSequence::create($this->lcsSolver->longestCommonSubsequence($a->getTokens(), $b->getTokens()));
    }
}