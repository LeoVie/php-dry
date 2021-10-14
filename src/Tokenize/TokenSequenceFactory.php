<?php

declare(strict_types=1);

namespace App\Tokenize;

use App\Wrapper\PhpTokenWrapper;

class TokenSequenceFactory
{
    public function __construct(
        private PhpTokenWrapper         $phpTokenWrapper,
        private TokenSequenceNormalizer $tokenSequenceNormalizer,
    )
    {
    }

    public function createNormalizedLevel1(string $code): TokenSequence
    {
        return $this->tokenSequenceNormalizer->normalizeLevel1(TokenSequence::create($this->phpTokenWrapper->tokenize($code)));
    }

    public function createNormalizedLevel2(TokenSequence $tokenSequence): TokenSequence
    {
        return $this->tokenSequenceNormalizer->normalizeLevel2($tokenSequence);
    }
}