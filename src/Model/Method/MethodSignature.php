<?php

declare(strict_types=1);

namespace App\Model\Method;

use JsonSerializable;

class MethodSignature implements JsonSerializable
{
    /**
     * @param string[] $paramTypes
     * @param int[] $paramsOrder
     */
    private function __construct(
        private array  $paramTypes,
        private array  $paramsOrder,
        private string $returnType,
    )
    {
    }

    /**
     * @param string[] $paramTypes
     * @param int[] $paramsOrder
     */
    public static function create(
        array  $paramTypes,
        array  $paramsOrder,
        string $returnType,
    ): self
    {
        return new self($paramTypes, $paramsOrder, $returnType);
    }

    /** @return string[] */
    public function getParamTypes(): array
    {
        return $this->paramTypes;
    }

    /** @return int[] */
    public function getParamsOrder(): array
    {
        return $this->paramsOrder;
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
