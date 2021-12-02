<?php

namespace App\Output\Html;

class Content
{
    private function __construct(
        private string $content
    )
    {
    }

    public static function create(string $content): self
    {
        return new self($content);
    }

    public function asCode(): string
    {
        return $this->content;
    }
}