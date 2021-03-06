<?php

declare(strict_types=1);

namespace App\Model\SourceCloneCandidate;

use App\Collection\MethodsCollection;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;

class Type1SourceCloneCandidate implements SourceCloneCandidate
{
    private function __construct(private TokenSequence $tokenSequence, private MethodsCollection $methodsCollection)
    {
    }

    public static function create(TokenSequence $tokenSequence, MethodsCollection $methodsCollection): self
    {
        return new self($tokenSequence, $methodsCollection);
    }

    public function getTokenSequence(): TokenSequence
    {
        return $this->tokenSequence;
    }

    public function getMethodsCollection(): MethodsCollection
    {
        return $this->methodsCollection;
    }
}
