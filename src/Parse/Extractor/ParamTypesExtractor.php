<?php

declare(strict_types=1);

namespace App\Parse\Extractor;

use App\Exception\OtherNodeTypeExpected;
use PhpParser\Node\Identifier;
use PhpParser\Node\Param;

class ParamTypesExtractor
{
    /**
     * @param Param[] $params
     *
     * @return string[]
     *
     * @throws OtherNodeTypeExpected
     */
    public function extractFromParamsList(array $params): array
    {
        return array_map(function (Param $param) {
            if (!$param->type instanceof Identifier) {
                $class = $param->type !== null ? $param->type::class : null;
                throw OtherNodeTypeExpected::create(Identifier::class, $class);
            }

            return $param->type->name;
        }, $params);
    }
}