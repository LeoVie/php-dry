<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class SubsequenceUtilNotFound extends Exception
{
    private function __construct(string $strategy)
    {
        parent::__construct(\Safe\sprintf('No SubsequenceUtil exists for strategy "%s".', $strategy));
    }

    public static function create(string $strategy): self
    {
        return new self($strategy);
    }
}