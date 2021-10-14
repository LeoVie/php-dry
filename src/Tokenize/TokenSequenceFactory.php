<?php

declare(strict_types=1);

namespace App\Tokenize;

use App\Wrapper\PhpTokenWrapper;

class TokenSequenceFactory
{
    public function __construct(private PhpTokenWrapper $phpTokenWrapper)
    {}

    public function createNormalizedLevel1(string $code): TokenSequence
    {
        return TokenSequence::create($this->phpTokenWrapper->tokenize($code))
            ->withoutOpenTag()
            ->withoutCloseTag()
            ->withoutAccessModifiers()
            ->withoutWhitespaces()
            ->withoutComments()
            ->withoutDocComments()
            ->filter();
    }
}