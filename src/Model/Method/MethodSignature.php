<?php

declare(strict_types=1);

namespace App\Model\Method;

use JsonSerializable;

class MethodSignature implements JsonSerializable
{
    /** @param string[] $paramTypes */
    private function __construct(
        private array  $paramTypes,
        private string $returnType,
    ) {
    }

    /** @param string[] $paramTypes */
    public static function create(
        array  $paramTypes,
        string $returnType,
    ): self {
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

    /** @return array{'paramTypes': string[], "returnType": string} */
    public function jsonSerialize(): array
    {
        return [
            'paramTypes' => $this->getParamTypes(),
            'returnType' => $this->getReturnType(),
        ];
    }
}
