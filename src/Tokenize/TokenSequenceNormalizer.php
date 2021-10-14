<?php

declare(strict_types=1);

namespace App\Tokenize;

use App\Tokenize\Normalize\NothingToNormalizeNormalizer;
use App\Tokenize\Normalize\TokenNormalizer;
use PhpToken;

class TokenSequenceNormalizer
{
    /** @param iterable<TokenNormalizer> $tokenNormalizers */
    public function __construct(
        private iterable                     $tokenNormalizers,
        private NothingToNormalizeNormalizer $nothingToNormalizeNormalizer,
    )
    {
    }

    public function normalizeLevel1(TokenSequence $tokenSequence): TokenSequence
    {
        return $tokenSequence
            ->withoutOpenTag()
            ->withoutCloseTag()
            ->withoutAccessModifiers()
            ->withoutWhitespaces()
            ->withoutComments()
            ->withoutDocComments()
            ->filter();
    }

    public function normalizeLevel2(TokenSequence $tokenSequence): TokenSequence
    {
        foreach ($this->tokenNormalizers as $tokenNormalizer) {
            $tokenNormalizer->reset();
        }

        return TokenSequence::create(
            array_map(
                fn(PhpToken $t): PhpToken => $this->findMatchingTokenNormalizer($t)->normalizeToken($t),
                $tokenSequence->getTokens()
            )
        );
    }

    private function findMatchingTokenNormalizer(PhpToken $token): TokenNormalizer
    {
        foreach ($this->tokenNormalizers as $tokenNormalizer) {
            if ($tokenNormalizer->supports($token)) {
                return $tokenNormalizer;
            }
        }

        return $this->nothingToNormalizeNormalizer;
    }
}