<?php

declare(strict_types=1);

namespace App\ModelOutput\Method;

use App\Model\Method\Method;
use App\ModelOutput\CodePosition\CodePositionRangeOutput;

class MethodOutput
{
    public function __construct(private CodePositionRangeOutput $codePositionRangeOutput)
    {
    }

    public function format(Method $method): string
    {
        return \Safe\sprintf(
            '%s: %s (%s)',
            $method->getFilepath(),
            $method->getName(),
            $this->codePositionRangeOutput->format($method->getCodePositionRange())
        );
    }
}