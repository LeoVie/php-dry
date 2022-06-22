<?php

declare(strict_types=1);

namespace App\Model\ClassModel;

use App\Model\Method\MethodSignature;
use JsonSerializable;

class ClassModel implements JsonSerializable
{
    /** @param class-string $FQN */
    private function __construct(
        private string           $FQN,
        private ?MethodSignature $constructorSignature
    )
    {
    }

    /** @param class-string $FQN */
    public static function create(string $FQN, ?MethodSignature $constructorSignature): self
    {
        return new self($FQN, $constructorSignature);
    }

    /** @return class-string */
    public function getFQN(): string
    {
        return $this->FQN;
    }

    public function getConstructorSignature(): ?MethodSignature
    {
        return $this->constructorSignature;
    }

    /** @return array{'FQN': class-string, 'constructorSignature': ?MethodSignature} */
    public function jsonSerialize(): array
    {
        return [
            'FQN' => $this->getFQN(),
            'constructorSignature' => $this->getConstructorSignature(),
        ];
    }
}