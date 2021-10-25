<?php

declare(strict_types=1);

namespace App\Factory\TokenSequenceRepresentative;

use App\Merge\Type2TokenSequenceRepresentativeMerger;
use App\Model\TokenSequenceRepresentative\Type1TokenSequenceRepresentative;
use App\Model\TokenSequenceRepresentative\Type2TokenSequenceRepresentative;
use LeoVie\PhpTokenNormalize\Service\TokenSequenceNormalizer;

class Type2TokenSequenceRepresentativeFactory
{
    public function __construct(
        private TokenSequenceNormalizer                $tokenSequenceNormalizer,
        private Type2TokenSequenceRepresentativeMerger $type2TokenSequenceRepresentativeMerger,
    )
    {
    }

    /**
     * @param Type1TokenSequenceRepresentative[] $type1TokenSequenceRepresentatives
     *
     * @return Type2TokenSequenceRepresentative[]
     */
    public function createMultiple(array $type1TokenSequenceRepresentatives): array
    {
        return $this->type2TokenSequenceRepresentativeMerger->merge(
            array_map(
                fn(Type1TokenSequenceRepresentative $tsr): Type2TokenSequenceRepresentative => Type2TokenSequenceRepresentative::create(
                    $this->tokenSequenceNormalizer->normalizeLevel2($tsr->getTokenSequence()),
                    $tsr->getMethodsCollection(),
                ), $type1TokenSequenceRepresentatives)
        );
    }
}