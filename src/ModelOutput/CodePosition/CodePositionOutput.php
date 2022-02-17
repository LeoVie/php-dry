<?php

declare(strict_types=1);

namespace App\ModelOutput\CodePosition;

use App\Model\CodePosition\CodePosition;

class CodePositionOutput
{
    public function format(CodePosition $codePosition): string
    {
        return \Safe\sprintf(
            '%s (position %s)',
            $codePosition->getLine(),
            $codePosition->getFilePos()
        );
    }
}