<?php

declare(strict_types=1);

namespace App\CloneDetection;

use App\Model\SourceClone\SourceClone;
use App\Model\TokenSequenceRepresentative\TokenSequenceRepresentative;

class Type1CloneDetector
{
    /**
     * @param TokenSequenceRepresentative[] $tokenSequenceRepresentatives
     *
     * @return SourceClone[]
     */
    public function detect(array $tokenSequenceRepresentatives): array
    {
        return array_map(
            fn(TokenSequenceRepresentative $tsr): SourceClone => SourceClone::createType1($tsr->getMethodsCollection()),
            array_filter(
                $tokenSequenceRepresentatives,
                fn(TokenSequenceRepresentative $sc): bool => $sc->getMethodsCollection()->count() > 1
            )
        );
    }
}