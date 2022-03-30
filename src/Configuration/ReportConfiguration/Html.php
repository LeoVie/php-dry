<?php

namespace App\Configuration\ReportConfiguration;

class Html
{
    private function __construct(private string $filepath)
    {}

    public static function create(string $filepath): self
    {
        return new self($filepath);
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }
}