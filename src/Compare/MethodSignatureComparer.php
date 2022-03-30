<?php

declare(strict_types=1);

namespace App\Compare;

use App\Model\Method\MethodSignature;

class MethodSignatureComparer
{
    public function areEqual(MethodSignature $a, MethodSignature $b): bool
    {
        return $this->sameParamTypes($a, $b) && $this->sameReturnType($a, $b);
    }

    private function sameParamTypes(MethodSignature $a, MethodSignature $b): bool
    {
        return $a->getParamTypes() === $b->getParamTypes();
    }

    private function sameReturnType(MethodSignature $a, MethodSignature $b): bool
    {
        return $a->getReturnType() === $b->getReturnType();
    }
}
