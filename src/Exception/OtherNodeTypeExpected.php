<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Safe\Exceptions\StringsException;

class OtherNodeTypeExpected extends Exception
{
    /** @throws StringsException */
    private function __construct(string $expectedNodeType, ?string $actualNodeType)
    {
        if ($actualNodeType === null) {
            $actualNodeType = 'NULL';
        }
        parent::__construct(\Safe\sprintf('Expected node type %s, but actual got %s.', $expectedNodeType, $actualNodeType));
    }

    /** @throws StringsException */
    public static function create(string $expectedNodeType, ?string $actualNodeType): self
    {
        return new self($expectedNodeType, $actualNodeType);
    }
}