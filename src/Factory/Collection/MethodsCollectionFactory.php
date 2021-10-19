<?php

declare(strict_types=1);

namespace App\Factory\Collection;

use App\Collection\MethodsCollection;
use App\Exception\CollectionCannotBeEmpty;
use App\Model\Method\HasMethod;
use App\Model\Method\Method;

class MethodsCollectionFactory
{
    /**
     * @param HasMethod[] $hasMethods
     * @throws CollectionCannotBeEmpty
     */
    public function fromHasMethods(array $hasMethods): MethodsCollection
    {
        return MethodsCollection::create(...array_map(fn(HasMethod $h): Method => $h->getMethod(), $hasMethods));
    }
}