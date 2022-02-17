<?php

declare(strict_types=1);

namespace App\Model\SourceClone;

use App\Collection\MethodsCollection;
use JsonSerializable;

class SourceClone implements JsonSerializable
{
    public const TYPE_1 = 'TYPE_1';
    public const TYPE_2 = 'TYPE_2';
    public const TYPE_3 = 'TYPE_3';
    public const TYPE_4 = 'TYPE_4';

    private function __construct(
        private string            $type,
        private MethodsCollection $methodsCollection,
    )
    {
    }

    public static function create(string $type, MethodsCollection $methodsCollection): self
    {
        return new self($type, $methodsCollection);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMethodsCollection(): MethodsCollection
    {
        return $this->methodsCollection;
    }

    /** @return array{'type': string, "methods": Method[]} */
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->getType(),
            'methods' => $this->getMethodsCollection()->getAll(),
        ];
    }
}