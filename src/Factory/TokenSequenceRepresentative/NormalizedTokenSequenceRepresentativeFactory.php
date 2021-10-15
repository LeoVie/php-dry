<?php

declare(strict_types=1);

namespace App\Factory\TokenSequenceRepresentative;

use App\Model\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentative;
use App\Model\TokenSequenceRepresentative\TokenSequenceRepresentative;
use App\Tokenize\TokenSequenceFactory;

class NormalizedTokenSequenceRepresentativeFactory
{
    public function __construct(
        private TokenSequenceFactory $tokenSequenceFactory,
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
                $this->tokenSequenceFactory->createNormalizedLevel2($tokenSequenceRepresentative->getTokenSequence()),
                $tokenSequenceRepresentative->getMethodsCollection(),
            );
        }, $tokenSequenceRepresentatives);
    }
}