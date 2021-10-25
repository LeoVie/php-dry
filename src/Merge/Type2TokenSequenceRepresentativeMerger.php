<?php

declare(strict_types=1);

namespace App\Merge;

use App\Collection\MethodsCollection;
use App\Exception\CollectionCannotBeEmpty;
use App\Model\TokenSequenceRepresentative\Type2TokenSequenceRepresentative;

class Type2TokenSequenceRepresentativeMerger
{
    /**
     * @param Type2TokenSequenceRepresentative[] $type2TokenSequenceRepresentatives
     *
     * @return Type2TokenSequenceRepresentative[]
     * @throws CollectionCannotBeEmpty
     */
    public function merge(array $type2TokenSequenceRepresentatives): array
    {
        /** @var Type2TokenSequenceRepresentative[] $mergedType2TokenSequenceRepresentatives */
        $mergedType2TokenSequenceRepresentatives = [];

        foreach ($type2TokenSequenceRepresentatives as $type2TokenSequenceRepresentative) {
            $mergedType2TokenSequenceRepresentatives = $this->addToExistingType2TokenSequenceRepresentativeOrCreateNewOne(
                $type2TokenSequenceRepresentative,
                $mergedType2TokenSequenceRepresentatives
            );
        }

        return $mergedType2TokenSequenceRepresentatives;
    }

    /**
     * @param Type2TokenSequenceRepresentative $type2TokenSequenceRepresentative
     * @param Type2TokenSequenceRepresentative[] $newType2TokenRepresentatives
     * @return Type2TokenSequenceRepresentative[]
     * @throws CollectionCannotBeEmpty
     */
    private function addToExistingType2TokenSequenceRepresentativeOrCreateNewOne(
        Type2TokenSequenceRepresentative $type2TokenSequenceRepresentative,
        array                            $newType2TokenRepresentatives,
    ): array
    {
        $identity = $type2TokenSequenceRepresentative->getTokenSequence()->identity();

        $newNormalizedMethods = $type2TokenSequenceRepresentative->getMethodsCollection()->getAll();
        if (!array_key_exists($identity, $newType2TokenRepresentatives)) {
            $newType2TokenRepresentatives[$identity] = Type2TokenSequenceRepresentative::create(
                $type2TokenSequenceRepresentative->getTokenSequence(),
                MethodsCollection::create(...$newNormalizedMethods),
            );

            return $newType2TokenRepresentatives;
        }

        foreach ($newNormalizedMethods as $newNormalizedMethod) {
            $newType2TokenRepresentatives[$identity]->getMethodsCollection()->add($newNormalizedMethod);
        }

        return array_values($newType2TokenRepresentatives);
    }
}