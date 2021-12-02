<?php

namespace App\Output;

class HtmlDOM
{
    private string $content = '<!doctype html>';

    public static function create(): self
    {
        return new self();
    }

    public function add(string $content): self
    {
        $this->content .= $content;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}