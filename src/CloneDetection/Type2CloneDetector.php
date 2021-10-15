<?php

declare(strict_types=1);

namespace App\CloneDetection;

use App\Merge\NormalizedTokenSequenceRepresentativeMerger;
use App\Model\SourceClone\SourceClone;
use App\Model\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentative;

class Type2CloneDetector
{
    public function __construct(private NormalizedTokenSequenceRepresentativeMerger $normalizedTokenSequenceRepresentativeMerger)
    {
    }

    /**
     * @param NormalizedTokenSequenceRepresentative[] $normalizedTokenSequenceRepresentatives
     *
     * @return SourceClone[]
     */
    public function detect(array $normalizedTokenSequenceRepresentatives): array
    {
        return array_map(
            fn(NormalizedTokenSequenceRepresentative $tsr): SourceClone => SourceClone::createType2($tsr->getMethodsCollection()),
            array_filter(
                $this->normalizedTokenSequenceRepresentativeMerger->merge($normalizedTokenSequenceRepresentatives),
                fn(NormalizedTokenSequenceRepresentative $sc): bool => $sc->getMethodsCollection()->count() > 1
            )
        );
    }
}