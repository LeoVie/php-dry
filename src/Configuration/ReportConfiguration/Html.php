<?php

namespace App\Configuration\ReportConfiguration;

class Html
{
    private function __construct(private string $directory)
    {}

    public static function create(string $directory): self
    {
        return new self($directory);
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }
}