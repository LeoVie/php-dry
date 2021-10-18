<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Safe\Exceptions\StringsException;

class InvalidPartBoundaries extends Exception
{
    /** @throws StringsException */
    private function __construct(int $start, int $end)
    {
        parent::__construct(\Safe\sprintf('Start boundary %s is greater than end boundary %s.', $start, $end));
    }

    /** @throws StringsException */
    public static function create(int $start, int $end): self
    {
        return new self($start, $end);
    }
}