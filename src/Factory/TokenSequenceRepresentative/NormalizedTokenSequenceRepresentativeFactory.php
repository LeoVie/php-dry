<?php

declare(strict_types=1);

namespace App\Factory\TokenSequenceRepresentative;

use App\Model\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentative;
use App\Model\TokenSequenceRepresentative\ExactTokenSequenceRepresentative;
use App\Tokenize\TokenSequenceNormalizer;

class NormalizedTokenSequenceRepresentativeFactory
{
    public function __construct(private TokenSequenceNormalizer $tokenSequenceNormalizer)
    {
    }

    /**
     * @param ExactTokenSequenceRepresentative[] $tokenSequenceRepresentatives
     *
     * @return NormalizedTokenSequenceRepresentative[]
     */
    public function normalizeMultipleTokenSequenceRepresentatives(array $tokenSequenceRepresentatives): array
    {
        return array_map(
            fn(ExactTokenSequenceRepresentative $tsr): NormalizedTokenSequenceRepresentative => NormalizedTokenSequenceRepresentative::create(
                $this->tokenSequenceNormalizer->normalizeLevel2($tsr->getTokenSequence()),
                $tsr->getMethodsCollection(),
            ), $tokenSequenceRepresentatives);
    }
}