<?php

declare(strict_types=1);

namespace App\OutputFormatter\Model\CodePosition;

use App\Model\CodePosition\CodePositionRange;

class CodePositionRangeOutputFormatter
{
    public function __construct(private CodePositionOutputFormatter $codePositionOutputFormatter)
    {
    }

    public function format(CodePositionRange $codePositionRange): string
    {
        return sprintf(
            '%s - %s (%s lines)',
            $this->codePositionOutputFormatter->format($codePositionRange->getStart()),
            $this->codePositionOutputFormatter->format($codePositionRange->getEnd()),
            $codePositionRange->countOfLines()
        );
    }
}
