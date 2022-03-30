<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class NoParamRequestForParamType extends Exception
{
    private function __construct(string $paramType)
    {
        parent::__construct(\Safe\sprintf('No ParamRequest exists for param type "%s".', $paramType));
    }

    public static function create(string $paramType): self
    {
        return new self($paramType);
    }
}
