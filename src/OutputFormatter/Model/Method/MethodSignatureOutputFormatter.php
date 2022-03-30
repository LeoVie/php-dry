<?php

declare(strict_types=1);

namespace App\OutputFormatter\Model\Method;

use App\Model\Method\MethodSignature;

class MethodSignatureOutputFormatter
{
    public function format(MethodSignature $methodSignature): string
    {
        return \Safe\sprintf(
            '(%s): %s',
            join(', ', $methodSignature->getParamTypes()),
            $methodSignature->getReturnType()
        );
    }
}
