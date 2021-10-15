<?php

declare(strict_types=1);

namespace App\Parse\Extractor;

use App\Exception\NodeTypeNotConvertable;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use Safe\Exceptions\StringsException;

class ParamTypesExtractor
{
    public function __construct(private NodeTypeToStringConverter $nodeTypeToStringConverter)
    {
    }

    /**
     * @return string[]
     *
     * @throws NodeTypeNotConvertable
     * @throws StringsException
     */
    public function extract(ClassMethod|Function_ $method): array
    {
        return array_map(fn(Param $p) => $this->nodeTypeToStringConverter->convert($p->type), $method->params);
    }
}