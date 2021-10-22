<?php

declare(strict_types=1);

namespace App\Factory;

use App\Model\Method\MethodSignature;
use LeoVie\PhpMethodsParser\Exception\NodeTypeNotConvertable;
use LeoVie\PhpMethodsParser\Extractor\ParamTypesExtractor;
use LeoVie\PhpMethodsParser\Extractor\ReturnTypeExtractor;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use Safe\Exceptions\StringsException;

class MethodSignatureFactory
{
    public function __construct(
        private ReturnTypeExtractor $returnTypeExtractor,
        private ParamTypesExtractor $paramTypesExtractor,
    )
    {
    }

    /**
     * @throws StringsException
     * @throws NodeTypeNotConvertable
     */
    public function create(ClassMethod|Function_ $method): MethodSignature
    {
        return MethodSignature::create(
            $this->paramTypesExtractor->extract($method),
            $this->returnTypeExtractor->extract($method),
        );
    }
}