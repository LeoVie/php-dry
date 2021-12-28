<?php

declare(strict_types=1);

namespace App\Collection;

use App\Exception\CollectionCannotBeEmpty;
use App\Model\Method\Method;

class MethodsCollection
{
    /** @var Method[] */
    private array $methods;

    /** @throws CollectionCannotBeEmpty */
    private function __construct(Method ...$methods)
    {
        if (empty($methods)) {
            throw CollectionCannotBeEmpty::create();
        }

        $this->methods = $methods;
    }

    /** @throws CollectionCannotBeEmpty */
    public static function create(Method ...$methods): self
    {
        return new self(...$methods);
    }

    public function getFirst(): Method
    {
        return $this->methods[array_key_first($this->methods)];
    }

    /** @return Method[] */
    public function getAll(): array
    {
        return $this->methods;
    }

    public function equals(self $other): bool
    {
        return $this->getAll() === $other->getAll();
    }

    public function add(Method $method): self
    {
        $this->methods[] = $method;

        return $this;
    }

    public function count(): int
    {
        return count($this->methods);
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