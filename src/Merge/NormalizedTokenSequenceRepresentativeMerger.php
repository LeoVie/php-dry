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
        /** @var NormalizedTokenSequenceRepresentative[] $newNormalizedTokenSequenceRepresentatives */
        $newNormalizedTokenSequenceRepresentatives = [];

        foreach ($normalizedTokenSequenceRepresentatives as $normalizedTokenSequenceRepresentative) {
            $newNormalizedTokenSequenceRepresentatives = $this->addToExistingNormalizedTokenSequenceRepresentativeOrCreateNewOne(
                $normalizedTokenSequenceRepresentative,
                $newNormalizedTokenSequenceRepresentatives
            );
        }

        return $newNormalizedTokenSequenceRepresentatives;
    }

    /**
     * @param NormalizedTokenSequenceRepresentative $normalizedTokenSequenceRepresentative
     * @param NormalizedTokenSequenceRepresentative[] $newNormalizedTokenRepresentatives
     * @return NormalizedTokenSequenceRepresentative[]
     */
    private function addToExistingNormalizedTokenSequenceRepresentativeOrCreateNewOne(
        NormalizedTokenSequenceRepresentative $normalizedTokenSequenceRepresentative,
        array                                 $newNormalizedTokenRepresentatives,
    ): array
    {
        $identity = $normalizedTokenSequenceRepresentative->getTokenSequence()->identity();

        $newNormalizedMethods = $normalizedTokenSequenceRepresentative->getMethodsCollection()->getAll();
        if (!array_key_exists($identity, $newNormalizedTokenRepresentatives)) {
            $newNormalizedTokenRepresentatives[$identity] = NormalizedTokenSequenceRepresentative::create(
                $normalizedTokenSequenceRepresentative->getTokenSequence(),
                MethodsCollection::create(...$newNormalizedMethods),
            );

            return $newNormalizedTokenRepresentatives;
        }

        foreach ($newNormalizedMethods as $newNormalizedMethod) {
            $newNormalizedTokenRepresentatives[$identity]->getMethodsCollection()->add($newNormalizedMethod);
        }

        return array_values($newNormalizedTokenRepresentatives);
    }
}