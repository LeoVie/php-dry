<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Safe\Exceptions\StringsException;

class NoReplacementRegistered extends Exception
{
    /** @throws StringsException */
    private function __construct(string $original)
    {
        parent::__construct(\Safe\sprintf('Node replacement registered for %s.', $original));
    }

    /** @throws StringsException */
    public static function create(string $original): self
    {
        return new self($original);
    }
}