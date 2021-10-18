<?php

declare(strict_types=1);

namespace App\Grouper;

use App\Collection\MethodsCollection;
use App\Compare\MethodSignatureComparer;
use App\Model\Method\Method;

class MethodsBySignatureGrouper
{
    public function __construct(private MethodSignatureComparer $methodSignatureComparer)
    {
    }

    /**
     * @param Method[] $methods
     * @return MethodsCollection[]
     */
    public function group(array $methods): array
    {
        if (empty($methods)) {
            return [];
        }

        /** @var MethodsCollection[] $methodsCollections */
        $methodsCollections = [
            MethodsCollection::create(array_shift($methods)),
        ];

        foreach ($methods as $method) {
            $methodsCollections = $this->addToExistingMatchingMethodsCollectionOrCreateNewOne($method, $methodsCollections);
        }

        return $methodsCollections;
    }

    /**
     * @param MethodsCollection[] $methodsCollections
     *
     * @return MethodsCollection[]
     */
    private function addToExistingMatchingMethodsCollectionOrCreateNewOne(Method $method, array $methodsCollections): array
    {
        $collection = $this->findMatchingMethodsCollection($method, $methodsCollections);

        if ($collection !== null) {
            $collection->add($method);

            return $methodsCollections;
        }

        $collection = MethodsCollection::create($method);
        $methodsCollections[] = $collection;

        return $methodsCollections;
    }

    /** @param MethodsCollection[] $methodsCollections */
    private function findMatchingMethodsCollection(Method $method, array $methodsCollections): ?MethodsCollection
    {
        foreach ($methodsCollections as $methodSignaturesCollection) {
            if ($this->matchesMethodSignaturesCollection($method, $methodSignaturesCollection)) {
                return $methodSignaturesCollection;
            }
        }

        return null;
    }

    private function matchesMethodSignaturesCollection(Method $method, MethodsCollection $methodsCollection): bool
    {
        return $this->methodSignatureComparer->areEqual(
            $method->getMethodSignature(),
            $methodsCollection->getFirst()->getMethodSignature()
        );
    }
}