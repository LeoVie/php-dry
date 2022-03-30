<?php

declare(strict_types=1);

namespace App\Model\SourceCloneCandidate;

use App\Collection\MethodsCollection;
use App\Model\Identity;
use LeoVie\PhpGrouper\Model\GroupIdentifiable;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;

class Type2SourceCloneCandidate implements SourceCloneCandidate, Identity, \Stringable, GroupIdentifiable
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

    public function identity(): string
    {
        return $this->getTokenSequence()->identity();
    }

    public function groupID(): string
    {
        return $this->identity();
    }

    public function __toString(): string
    {
        return $this->identity();
    }
}
