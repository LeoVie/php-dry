<?php

declare(strict_types=1);

namespace App\OutputFormatter\Model\Method;

use App\Model\Method\MethodSignature;

class MethodSignatureOutputFormatter
{
    public function format(MethodSignature $methodSignature): string
    {
        $orderedParamTypes = array_combine($methodSignature->getParamsOrder(), $methodSignature->getParamTypes());
        ksort($orderedParamTypes);

        return sprintf(
            '(%s): %s',
            join(', ', $orderedParamTypes),
            $methodSignature->getReturnType()
        );
    }
}
