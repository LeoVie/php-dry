<?php

declare(strict_types=1);

namespace App\Model\TokenSequenceRepresentative;

use App\Collection\MethodsCollection;
use App\Model\Identity;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;

class Type3TokenSequenceRepresentative implements Identity, \Stringable, TokenSequenceRepresentative
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