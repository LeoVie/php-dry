<?php

declare(strict_types=1);

namespace App\Merge;

use App\Collection\MethodsCollection;
use App\Exception\CollectionCannotBeEmpty;
use App\Model\TokenSequenceRepresentative\Type3TokenSequenceRepresentative;

class Type3TokenSequenceRepresentativeMerger
{
    /**
     * @param array<Type3TokenSequenceRepresentative[]> $groups
     * @return Type3TokenSequenceRepresentative[]
     *
     * @throws CollectionCannotBeEmpty
     */
    public function merge(array $groups): array
    {
        $mergedType3TokenSequenceRepresentatives = [];
        foreach ($groups as $type3TokenSequenceRepresentatives) {
            $tokenSequences = [];
            $methods = [];
            foreach ($type3TokenSequenceRepresentatives as $type3TokenSequenceRepresentative) {
                $tokenSequences = array_merge(
                    $tokenSequences,
                    $type3TokenSequenceRepresentative->getTokenSequences()
                );
                $methods = array_merge($methods, $type3TokenSequenceRepresentative->getMethodsCollection()->getAll());
            }

            $mergedType3TokenSequenceRepresentatives[] = Type3TokenSequenceRepresentative::create(
                $tokenSequences,
                MethodsCollection::create(...$methods)
            );
        }

        return $mergedType3TokenSequenceRepresentatives;
    }
}