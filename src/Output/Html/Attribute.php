<?php

namespace App\Output\Html;

class Attribute
{
    private function __construct(private string $key, private string $value)
    {
    }

    public static function create(string $key, string $value): self
    {
        return new self($key, $value);
    }

    public function asCode(): string
    {
        return \Safe\sprintf('%s="%s"', $this->key, $this->value);
    }
}