<?php

declare(strict_types=1);

namespace App\Model\Method;

use App\Collection\MethodsCollection;

class MethodSignatureGroup
{
    private function __construct(
        private MethodSignature   $methodSignature,
        private MethodsCollection $methodsCollection,
    ) {
    }

    public static function create(MethodSignature $methodSignature, MethodsCollection $methodsCollection): self
    {
        return new self($methodSignature, $methodsCollection);
    }

    public function getMethodSignature(): MethodSignature
    {
        return $this->methodSignature;
    }

    public function getMethodsCollection(): MethodsCollection
    {
        return $this->methodsCollection;
    }
}
