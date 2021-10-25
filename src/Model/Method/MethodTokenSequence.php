<?php

declare(strict_types=1);

namespace App\Model\Method;

use App\Model\Identity;
use LeoVie\PhpGrouper\Model\GroupIdentifiable;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use Stringable;

class MethodTokenSequence implements Stringable, Identity, GroupIdentifiable
{
    private function __construct(private Method $method, private TokenSequence $tokenSequence)
    {
    }

    public static function create(Method $method, TokenSequence $tokenSequence): self
    {
        return new self($method, $tokenSequence);
    }

    public function getMethod(): Method
    {
        return $this->method;
    }

    public function getTokenSequence(): TokenSequence
    {
        return $this->tokenSequence;
    }

    public function identity(): string
    {
        return $this->getTokenSequence()->identity();
    }

    public function groupID(): string
    {
        return $this->identity();
    }

    public function __toString(): string
    {
        return $this->identity();
    }
}