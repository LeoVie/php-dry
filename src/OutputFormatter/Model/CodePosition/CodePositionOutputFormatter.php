<?php

declare(strict_types=1);

namespace App\OutputFormatter\Model\CodePosition;

use App\Model\CodePosition\CodePosition;

class CodePositionOutputFormatter
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