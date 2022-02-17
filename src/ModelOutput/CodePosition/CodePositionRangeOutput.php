<?php

declare(strict_types=1);

namespace App\ModelOutput\CodePosition;

use App\Model\CodePosition\CodePositionRange;

class CodePositionRangeOutput
{
    public function __construct(private CodePositionOutput $codePositionOutput)
    {
    }

    public function format(CodePositionRange $codePositionRange): string
    {
        return \Safe\sprintf(
            '%s - %s (%s lines)',
            $this->codePositionOutput->format($codePositionRange->getStart()),
            $this->codePositionOutput->format($codePositionRange->getEnd()),
            $codePositionRange->countOfLines()
        );
    }
}