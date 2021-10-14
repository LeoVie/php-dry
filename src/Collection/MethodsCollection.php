<?php

declare(strict_types=1);

namespace App\Collection;

use App\Model\Method\Method;
use loophp\collection\Collection;
use loophp\collection\Contract\Collection as CollectionInterface;

class MethodsCollection
{
    /** @var CollectionInterface<int, Method> */
    private CollectionInterface $methods;

    public static function empty(): self
    {
        return new self();
    }

    public static function withInitialContent(Method ...$methods): self
    {
        $methodsCollection = self::empty();

        foreach ($methods as $method) {
            $methodsCollection->add($method);
        }

        return $methodsCollection;
    }

    private function __construct()
    {
        $this->methods = Collection::empty();
    }

    public function getFirst(): ?Method
    {
        return $this->methods->first()->current();
    }

    /** @return Method[] */
    public function getAll(): array
    {
        return $this->methods->normalize()->all();
    }

    public function add(Method $method): self
    {
        $this->methods = $this->methods->append($method);

        return $this;
    }

    public function count(): int
    {
        return $this->methods->count();
    }

    /** @return string[] */
    public function extractParamTypes(): array
    {
        $first = $this->getFirst();
        if ($first === null) {
            return [];
        }

        return $first->getMethodSignature()->getParamTypes();
    }
}