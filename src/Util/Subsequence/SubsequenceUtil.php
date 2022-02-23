<?php

declare(strict_types=1);

namespace App\Util\Subsequence;

interface SubsequenceUtil
{
    public function percentageOfSimilarText(string $a, string $b): int;
}