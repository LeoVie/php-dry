<?php

declare(strict_types=1);

namespace App\OutputFormatter\Model\SourceClone;

use App\Model\Method\Method;
use App\Model\SourceClone\SourceClone;
use App\OutputFormatter\Model\Method\MethodOutputFormatter;

class SourceCloneOutputFormatter
{
    public function __construct(private MethodOutputFormatter $methodOutputFormatter)
    {
    }

    public function format(SourceClone $sourceClone): string
    {
        return sprintf(
            "CLONE: Type: %s, Methods: \n\t%s",
            $sourceClone->getType(),
            join(
                "\n\t",
                array_map(
                    fn (Method $m) => $this->methodOutputFormatter->format($m),
                    $sourceClone->getMethodsCollection()->getAll()
                )
            )
        );
    }
}
