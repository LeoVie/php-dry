<?php

declare(strict_types=1);

namespace App\Factory\TokenSequenceRepresentative;

use App\Model\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentative;
use App\Model\TokenSequenceRepresentative\TokenSequenceRepresentative;
use App\Tokenize\TokenSequenceNormalizer;

class NormalizedTokenSequenceRepresentativeFactory
{
    public function __construct(private TokenSequenceNormalizer $tokenSequenceNormalizer)
    {
    }

    /**
     * @param TokenSequenceRepresentative[] $tokenSequenceRepresentatives
     *
     * @return NormalizedTokenSequenceRepresentative[]
     */
    public function normalizeMultipleTokenSequenceRepresentatives(array $tokenSequenceRepresentatives): array
    {
        return array_map(
            fn(TokenSequenceRepresentative $tsr): NormalizedTokenSequenceRepresentative => NormalizedTokenSequenceRepresentative::create(
                $this->tokenSequenceNormalizer->normalizeLevel2($tsr->getTokenSequence()),
                $tsr->getMethodsCollection(),
            ), $tokenSequenceRepresentatives);
    }
}