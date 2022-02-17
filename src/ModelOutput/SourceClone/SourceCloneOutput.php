<?php

declare(strict_types=1);

namespace App\ModelOutput\SourceClone;

use App\Model\Method\Method;
use App\Model\SourceClone\SourceClone;
use App\ModelOutput\Method\MethodOutput;

class SourceCloneOutput
{
    public function __construct(private MethodOutput $methodOutput)
    {
    }

    public function format(SourceClone $sourceClone): string
    {
        return \Safe\sprintf(
            "CLONE: Type: %s, Methods: \n\t%s",
            $sourceClone->getType(),
            join(
                "\n\t",
                array_map(
                    fn(Method $m) => $this->methodOutput->format($m),
                    $sourceClone->getMethodsCollection()->getAll()
                )
            )
        );
    }
}