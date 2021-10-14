<?php

declare(strict_types=1);

namespace App\Model\Method;

use Safe\Exceptions\StringsException;
use Stringable;

class MethodSignature implements Stringable
{
    /** @param string[] $paramTypes */
    private function __construct(
        private array  $paramTypes,
        private string $returnType,
    )
    {
    }

    /** @param string[] $paramTypes */
    public static function create(
        array  $paramTypes,
        string $returnType,
    ): self
    {
        return new self($paramTypes, $returnType);
    }

    /** @return string[] */
    public function getParamTypes(): array
    {
        return $this->paramTypes;
    }

    public function getReturnType(): string
    {
        return $this->returnType;
    }

    /** @throws StringsException */
    public function __toString(): string
    {
        return \Safe\sprintf('(%s): %s', join(', ', $this->getParamTypes()), $this->getReturnType());
    }
}