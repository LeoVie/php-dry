<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class OutputFormatNotFound extends Exception
{
    private function __construct(string $outputFormatName)
    {
        parent::__construct(\Safe\sprintf('Output format "%s" not found.', $outputFormatName));
    }

    public static function create(string $outputFormatName): self
    {
        return new self($outputFormatName);
    }
}