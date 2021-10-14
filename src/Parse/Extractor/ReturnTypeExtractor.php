<?php

declare(strict_types=1);

namespace App\Parse\Extractor;

use App\Exception\OtherNodeTypeExpected;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;

class ReturnTypeExtractor
{
    /** @throws OtherNodeTypeExpected */
    public function extractFromClassMethodOrFunction(ClassMethod|Function_ $function): Identifier
    {
        $returnType = $function->getReturnType();
        if (!$returnType instanceof Identifier) {
            $class = $returnType !== null ? $returnType::class : null;
            throw OtherNodeTypeExpected::create(Identifier::class, $class);
        }

        return $returnType;
    }
}