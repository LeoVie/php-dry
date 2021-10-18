<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Safe\Exceptions\StringsException;

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