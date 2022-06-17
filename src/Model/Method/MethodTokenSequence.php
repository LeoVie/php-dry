<?php

declare(strict_types=1);

namespace App\Model\Method;

use App\Model\Identity;
use LeoVie\PhpGrouper\Model\GroupIdentifiable;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;

class MethodTokenSequence implements Identity, GroupIdentifiable
{
    private function __construct(
        private Method        $method,
        private TokenSequence $tokenSequence,
        private string        $identity
    )
    {
    }

    public static function create(Method $method, TokenSequence $tokenSequence, string $identity): self
    {
        return new self($method, $tokenSequence, $identity);
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
        return $this->identity;
    }

    public function groupID(): string
    {
        return $this->identity;
    }
}
