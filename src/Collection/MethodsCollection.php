<?php

declare(strict_types=1);

namespace App\Collection;

use App\Exception\CollectionCannotBeEmpty;
use App\Model\Method\Method;

class MethodsCollection
{
    /** @var array<Method> */
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
        usort($methods, function (Method $m1, Method $m2): int {
            if ($m1->identity() === $m2->identity()) {
                return 0;
            }

            return $m1->identity() < $m2->identity() ? -1 : 1;
        });

        return new self(...$methods);
    }

    public function getFirst(): Method
    {
        /** @var array-key $firstArrayKey */
        $firstArrayKey = array_key_first($this->methods);

        return $this->methods[$firstArrayKey];
    }

    /** @return array<Method> */
    public function getAll(): array
    {
        $methods = $this->methods;

        usort($methods, function (Method $m1, Method $m2): int {
            if ($m1->identity() === $m2->identity()) {
                return 0;
            }

            return $m1->identity() < $m2->identity() ? -1 : 1;
        });

        return $methods;
    }

    public function equals(self $other): bool
    {
        return $this->containsOtherMethodCollection($other) && $other->containsOtherMethodCollection($this);
    }

    private function containsOtherMethodCollection(self $other): bool
    {
        foreach ($other->getAll() as $otherMethod) {
            if (!$this->contains($otherMethod)) {
                return false;
            }
        }

        return true;
    }

    private function contains(Method $method): bool
    {
        foreach ($this->getAll() as $thisMethod) {
            if ($thisMethod->identity() === $method->identity()) {
                return true;
            }
        }

        return false;
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