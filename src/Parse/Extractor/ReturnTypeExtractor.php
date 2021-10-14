<?php

declare(strict_types=1);

namespace App\Parse\Extractor;

use App\Exception\NodeTypeNotConvertable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use Safe\Exceptions\StringsException;

class ReturnTypeExtractor
{
    public function __construct(private NodeTypeToStringConverter $nodeTypeToStringConverter)
    {
    }

    /**
     * @throws StringsException
     * @throws NodeTypeNotConvertable
     */
    public function extractFromClassMethodOrFunction(ClassMethod|Function_ $function): string
    {
        return $this->nodeTypeToStringConverter->convert($function->getReturnType());
    }
}