<?php

declare(strict_types=1);

namespace App\Model\ClassModel;

use App\Model\Method\MethodSignature;
use JsonSerializable;

class ClassModel implements JsonSerializable
{
    private function __construct(
        private string           $FQN,
        private ?MethodSignature $constructorSignature
    )
    {
    }

    public static function create(string $FQN, ?MethodSignature $constructorSignature): self
    {
        return new self($FQN, $constructorSignature);
    }

    public function getFQN(): string
    {
        return $this->FQN;
    }

    public function getConstructorSignature(): ?MethodSignature
    {
        return $this->constructorSignature;
    }

    public function jsonSerialize(): array
    {
        return [
            'FQN' => $this->getFQN(),
            'constructorSignature' => $this->getConstructorSignature(),
        ];
    }
}