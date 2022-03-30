<?php

declare(strict_types=1);

namespace App\Factory\Collection;

use App\Collection\MethodsCollection;
use App\Exception\CollectionCannotBeEmpty;
use App\Model\Method\Method;
use App\Model\Method\MethodTokenSequence;

class MethodsCollectionFactory
{
    /**
     * @param MethodTokenSequence[] $methodTokenSequences
     * @throws CollectionCannotBeEmpty
     */
    public function fromMethodTokenSequence(array $methodTokenSequences): MethodsCollection
    {
        return MethodsCollection::create(...array_map(fn (MethodTokenSequence $mts): Method => $mts->getMethod(), $methodTokenSequences));
    }
}
