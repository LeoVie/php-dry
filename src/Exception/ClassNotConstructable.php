<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class ClassNotConstructable extends Exception
{
    private function __construct(string $class)
    {
        parent::__construct(sprintf('Class "%s" is not constructable via __construct.', $class));
    }

    public static function create(string $class): self
    {
        return new self($class);
    }
}
