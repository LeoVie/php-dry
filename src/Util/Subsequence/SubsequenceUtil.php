<?php

declare(strict_types=1);

namespace App\Util\Subsequence;

interface SubsequenceUtil
{
    public function isOverThreshold(string $a, string $b, int $threshold): bool;
}