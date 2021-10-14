<?php

declare(strict_types=1);

namespace App\Parse\Extractor;

use App\Exception\NodeTypeNotConvertable;
use PhpParser\Node\Param;
use Safe\Exceptions\StringsException;

class ParamTypesExtractor
{
    public function __construct(private NodeTypeToStringConverter $nodeTypeToStringConverter)
    {
    }

    /**
     * @param Param[] $params
     *
     * @return string[]
     *
     * @throws NodeTypeNotConvertable
     * @throws StringsException
     */
    public function extractFromParamsList(array $params): array
    {
        return array_map(fn(Param $p) => $this->nodeTypeToStringConverter->convert($p->type), $params);
    }
}