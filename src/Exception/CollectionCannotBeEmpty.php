<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class CollectionCannotBeEmpty extends Exception
{
    private function __construct()
    {
        parent::__construct(\Safe\sprintf('Collection is not allowed to be empty.'));
    }

    public static function create(): self
    {
        return new self();
    }
}