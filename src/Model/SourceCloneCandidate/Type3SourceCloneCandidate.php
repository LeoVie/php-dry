<?php

declare(strict_types=1);

namespace App\Model\SourceCloneCandidate;

use App\Collection\MethodsCollection;
use App\Model\Identity;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;

class Type3SourceCloneCandidate implements Identity, \Stringable, SourceCloneCandidate
{
    /** @param TokenSequence[] $tokenSequences */
    private function __construct(private array $tokenSequences, private MethodsCollection $methodsCollection)
    {}

    /** @param TokenSequence[] $tokenSequences */
    public static function create(array $tokenSequences, MethodsCollection $methodsCollection): self
    {
        return new self($tokenSequences, $methodsCollection);
    }

    /** @return TokenSequence[] */
    public function getTokenSequences(): array
    {
        return $this->tokenSequences;
    }

    public function getMethodsCollection(): MethodsCollection
    {
        return $this->methodsCollection;
    }

    public function identity(): string
    {
        return join('-', array_map(fn(TokenSequence $ts): string => $ts->identity(), $this->getTokenSequences()));
    }

    public function __toString(): string
    {
        return $this->identity();
    }
}