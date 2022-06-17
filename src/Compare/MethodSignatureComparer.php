<?php

declare(strict_types=1);

namespace App\Compare;

use App\Model\Method\MethodSignature;

class MethodSignatureComparer
{
    public function areEqual(MethodSignature $a, MethodSignature $b): bool
    {
        return $a->getHash() === $b->getHash();
    }
}
