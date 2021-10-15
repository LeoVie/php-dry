<?php

declare(strict_types=1);

namespace App\Merge;

use App\Collection\MethodsCollection;
use App\Model\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentative;

class NormalizedTokenSequenceRepresentativeMerger
{
    /**
     * @param NormalizedTokenSequenceRepresentative[] $normalizedTokenSequenceRepresentatives
     *
     * @return NormalizedTokenSequenceRepresentative[]
     */
    public function merge(array $normalizedTokenSequenceRepresentatives): array
    {
        /** @var NormalizedTokenSequenceRepresentative[] $newNormalizedTokenRepresentatives */
        $newNormalizedTokenRepresentatives = [];

        foreach ($normalizedTokenSequenceRepresentatives as $normalizedTokenSequenceRepresentative) {
            $identity = $normalizedTokenSequenceRepresentative->getTokenSequence()->identity();

            if (!array_key_exists($identity, $newNormalizedTokenRepresentatives)) {
                $newNormalizedTokenRepresentatives[$identity] = NormalizedTokenSequenceRepresentative::create(
                    $normalizedTokenSequenceRepresentative->getTokenSequence(),
                    MethodsCollection::empty(),
                );
            }

            $newNormalizedTokenRepresentatives[$identity] = NormalizedTokenSequenceRepresentative::create(
                $normalizedTokenSequenceRepresentative->getTokenSequence(),
                MethodsCollection::withInitialContent(
                    ...$newNormalizedTokenRepresentatives[$identity]->getMethodsCollection()->getAll(),
                    ...$normalizedTokenSequenceRepresentative->getMethodsCollection()->getAll()
                )
            );
        }

        return $newNormalizedTokenRepresentatives;
    }
}