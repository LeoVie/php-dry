<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class PhpDocumentorFailed extends Exception
{
    private function __construct(string $output)
    {
        parent::__construct('PhpDocumentor failed with output "' . $output . '".');
    }

    public static function create(string $output): self
    {
        return new self($output);
    }
}
