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
            MethodsCollection::withInitialContent(array_shift($methods)),
        ];

        foreach ($methods as $method) {
            $collection = $this->findMatchingMethodsCollection($method, $methodsCollections);

            if ($collection === null) {
                $collection = MethodsCollection::empty();
                $methodsCollections[] = $collection;
            }

            $collection->add($method);
        }

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
        $first = $methodsCollection->getFirst();
        if ($first === null) {
            return false;
        }

        return $this->methodSignatureComparer->areEqual($method->getMethodSignature(), $first->getMethodSignature());
    }
}