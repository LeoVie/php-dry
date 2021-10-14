<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Safe\Exceptions\StringsException;

class NodeTypeNotConvertable extends Exception
{
    /** @throws StringsException */
    private function __construct(string $nodeType)
    {
        parent::__construct(\Safe\sprintf('Node type %s is not convertable.', $nodeType));
    }

    /** @throws StringsException */
    public static function create(string $nodeType): self
    {
        return new self($nodeType);
    }
}