<?php

declare(strict_types=1);

namespace App\Model\SourceCloneCandidate;

use App\Collection\MethodsCollection;

class Type4SourceCloneCandidate implements SourceCloneCandidate
{
    private function __construct(private MethodsCollection $methodsCollection)
    {
    }

    public static function create(MethodsCollection $methodsCollection): self
    {
        return new self($methodsCollection);
    }

    public function getMethodsCollection(): MethodsCollection
    {
        return $this->methodsCollection;
    }
}