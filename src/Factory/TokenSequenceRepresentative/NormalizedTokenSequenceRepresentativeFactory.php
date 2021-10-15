<?php

declare(strict_types=1);

namespace App\Factory\TokenSequenceRepresentative;

use App\Model\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentative;
use App\Model\TokenSequenceRepresentative\TokenSequenceRepresentative;
use App\Tokenize\TokenSequenceNormalizer;

class NormalizedTokenSequenceRepresentativeFactory
{
    public function __construct(
        private TokenSequenceNormalizer $tokenSequenceNormalizer,
    )
    {
    }

    /**
     * @param TokenSequenceRepresentative[] $tokenSequenceRepresentatives
     *
     * @return NormalizedTokenSequenceRepresentative[]
     */
    public function createMultipleByTokenSequenceRepresentatives(array $tokenSequenceRepresentatives): array
    {
        return array_map(function (TokenSequenceRepresentative $tokenSequenceRepresentative): NormalizedTokenSequenceRepresentative {
            return NormalizedTokenSequenceRepresentative::create(
                $this->tokenSequenceNormalizer->normalizeLevel2($tokenSequenceRepresentative->getTokenSequence()),
                $tokenSequenceRepresentative->getMethodsCollection(),
            );
        }, $tokenSequenceRepresentatives);
    }
}