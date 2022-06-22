<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class NoParamRequestForParamType extends Exception
{
    private function __construct(string $paramType, string $paramClass)
    {
        parent::__construct(sprintf(
            'No ParamRequest exists for param type "%s" (class "%s)".',
            $paramType,
            $paramClass
        ));
    }

    public static function create(string $paramType, string $paramClass): self
    {
        return new self($paramType, $paramClass);
    }
}
