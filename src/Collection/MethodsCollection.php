<?php

declare(strict_types=1);

namespace App\Collection;

use App\Exception\CollectionCannotBeEmpty;
use App\Model\Method\Method;
use loophp\collection\Collection;
use loophp\collection\Contract\Collection as CollectionInterface;

class MethodsCollection
{
    /** @var CollectionInterface<int, Method> */
    private CollectionInterface $methods;

    /** @throws CollectionCannotBeEmpty */
    private function __construct(Method ...$methods)
    {
        if (empty($methods)) {
            throw CollectionCannotBeEmpty::create();
        }

        $this->methods = Collection::empty();
        foreach ($methods as $method) {
            $this->add($method);
        }
    }

    /** @throws CollectionCannotBeEmpty */
    public static function create(Method ...$methods): self
    {
        return new self(...$methods);
    }

    public function getFirst(): Method
    {
        /** @var Method $first */
        $first = $this->methods->first()->current();

        return $first;
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

    // TODO: Remove this unused method
    public function remove(Method $method): self
    {
        $withoutMethod = $this->methods->filter(fn(Method $m): bool => $m->identity() !== $method->identity());
        if ($withoutMethod->count() === 0) {
            throw CollectionCannotBeEmpty::create();
        }

        $this->methods = $withoutMethod;

        return $this;
    }

    public function count(): int
    {
        return $this->methods->count();
    }

    /** @return string[] */
    public function extractParamTypes(): array
    {
        return $this->getFirst()->getMethodSignature()->getParamTypes();
    }

    public function __toString(): string
    {
        return join("\n", array_map(fn(Method $m): string => $m->__toString(), $this->getAll()));
    }
}