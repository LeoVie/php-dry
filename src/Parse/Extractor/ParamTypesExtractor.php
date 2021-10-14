<?php

declare(strict_types=1);

namespace App\Parse\Extractor;

use App\Exception\OtherNodeTypeExpected;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
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
            if ($param->type === null) {
                return 'unclassified';
            }
            if ($param->type instanceof Identifier) {
                return $param->type->name;
            }
            if ($param->type instanceof Name) {
                return $param->type->toString();
            }
            if ($param->type instanceof NullableType) {
                if ($param->type->type instanceof Identifier) {
                    return '?' . $param->type->type->name;
                }
                if ($param->type->type instanceof Name) {
                    return '?' . $param->type->type->toString();
                }
            }

            throw OtherNodeTypeExpected::create(Identifier::class, $param->type::class);
        }, $params);
    }
}