<?php

declare(strict_types=1);

namespace App\OutputFormatter\Model\Method;

use App\Model\Method\Method;
use App\OutputFormatter\Model\CodePosition\CodePositionRangeOutputFormatter;

class MethodOutputFormatter
{
    public function __construct(private CodePositionRangeOutputFormatter $codePositionRangeOutputFormatter)
    {
    }

    public function format(Method $method): string
    {
        return \Safe\sprintf(
            '%s: %s (%s)',
            $method->getFilepath(),
            $method->getName(),
            $this->codePositionRangeOutputFormatter->format($method->getCodePositionRange())
        );
    }
}
