<?php

declare(strict_types=1);

namespace App\Parse\Extractor;

use App\Exception\OtherNodeTypeExpected;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;

class ReturnTypeExtractor
{
    /** @throws OtherNodeTypeExpected */
    public function extractFromClassMethodOrFunction(ClassMethod|Function_ $function): ?string
    {
        $returnType = $function->getReturnType();

        if ($returnType === null) {
            return 'void';
        }
        if ($returnType instanceof Identifier) {
            return $returnType->name;
        }
        if ($returnType instanceof Name) {
            return $returnType->toString();
        }
        if ($returnType instanceof NullableType) {
            if ($returnType->type instanceof Identifier) {
                return '?' . $returnType->type->name;
            }
            if ($returnType->type instanceof Name) {
                return '?' . $returnType->type->toString();
            }
        }

        throw OtherNodeTypeExpected::create(Identifier::class, $returnType::class);
    }
}