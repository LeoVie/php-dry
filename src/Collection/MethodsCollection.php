<?php

declare(strict_types=1);

namespace App\Collection;

use App\Exception\CollectionCannotBeEmpty;
use App\Model\Method\Method;
use App\ModelOutput\Method\MethodOutput;

class MethodsCollection
{
    private const METHODS_EQUAL = 0;
    private const METHOD_A_LOWER_B = -1;
    private const METHOD_B_LOWER_A = 1;

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
        $methods = self::sortMethods($methods);

        return new self(...$methods);
    }

    /**
     * @param array<Method> $methods
     *
     * @return array<Method>
     */
    private static function sortMethods(array $methods): array
    {
        usort($methods, function (Method $m1, Method $m2): int {
            if ($m1->identity() === $m2->identity()) {
                return self::METHODS_EQUAL;
            }

            return $m1->identity() <= $m2->identity() ? self::METHOD_A_LOWER_B : self::METHOD_B_LOWER_A;
        });

        return $methods;
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
        return self::sortMethods($this->methods);
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

    public function __toString(): string
    {
        return join(
            "\n",
            array_map(
                fn(Method $m): string => MethodOutput::create($m)->format()
                , $this->getAll()
            )
        );
    }
}