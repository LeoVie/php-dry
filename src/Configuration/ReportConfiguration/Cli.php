<?php

namespace App\Configuration\ReportConfiguration;

class Cli
{
    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }
}