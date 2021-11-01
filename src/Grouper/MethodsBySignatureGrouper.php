<?php

declare(strict_types=1);

namespace App\Grouper;

use App\Collection\MethodsCollection;
use App\Compare\MethodSignatureComparer;
use App\Model\Method\Method;
use App\Model\Method\MethodSignatureGroup;

class MethodsBySignatureGrouper
{
    public function __construct(private MethodSignatureComparer $methodSignatureComparer)
    {
    }

    /**
     * @param Method[] $methods
     *
     * @return MethodSignatureGroup[]
     */
    public function group(array $methods): array
    {
        if (empty($methods)) {
            return [];
        }

        $firstMethod = array_shift($methods);
        $methodSignatureGroups = [
            MethodSignatureGroup::create(
                $firstMethod->getMethodSignature(),
                MethodsCollection::create($firstMethod)
            ),
        ];

        foreach ($methods as $method) {
            $methodSignatureGroups = $this->addToExistingMatchingMethodSignatureGroupOrCreateNewOne($method, $methodSignatureGroups);
        }

        return $methodSignatureGroups;
    }

    /**
     * @param MethodSignatureGroup[] $methodSignatureGroups
     *
     * @return MethodSignatureGroup[]
     */
    private function addToExistingMatchingMethodSignatureGroupOrCreateNewOne(Method $method, array $methodSignatureGroups): array
    {
        $methodSignatureGroup = $this->findMatchingMethodSignatureGroup($method, $methodSignatureGroups);

        if ($methodSignatureGroup !== null) {
            $methodSignatureGroup->getMethodsCollection()->add($method);

            return $methodSignatureGroups;
        }

        $methodSignatureGroup = MethodSignatureGroup::create(
            $method->getMethodSignature(),
            MethodsCollection::create($method)
        );
        $methodSignatureGroups[] = $methodSignatureGroup;

        return $methodSignatureGroups;
    }

    /**
     * @param MethodSignatureGroup[] $methodSignatureGroups
     */
    private function findMatchingMethodSignatureGroup(Method $method, array $methodSignatureGroups): ?MethodSignatureGroup
    {
        foreach ($methodSignatureGroups as $methodSignatureGroup) {
            if ($this->matchesMethodSignatureGroup($method, $methodSignatureGroup)) {
                return $methodSignatureGroup;
            }
        }

        return null;
    }

    private function matchesMethodSignatureGroup(Method $method, MethodSignatureGroup $methodSignatureGroup): bool
    {
        return $this->methodSignatureComparer->areEqual(
            $method->getMethodSignature(),
            $methodSignatureGroup->getMethodSignature()
        );
    }
}