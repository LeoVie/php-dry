<?php

declare(strict_types=1);

namespace App\Factory\MethodSignature;

use App\Exception\NodeTypeNotConvertable;
use App\Factory\CodePosition\CodePositionRangeFactory;
use App\Model\CodePosition\CodePositionRange;
use App\Model\FilepathMethods\FilepathMethods;
use App\Model\Method\Method;
use App\Model\Method\MethodSignature;
use App\Parse\Extractor\ParamTypesExtractor;
use App\Parse\Extractor\ReturnTypeExtractor;
use App\Service\FileSystem;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use Safe\Exceptions\StringsException;

class MethodFromFilepathMethodsFactory
{
    public function __construct(
        private ReturnTypeExtractor      $returnTypeExtractor,
        private ParamTypesExtractor      $paramTypesExtractor,
        private CodePositionRangeFactory $codePositionRangeFactory,
        private FileSystem               $fileSystem,
    )
    {
    }

    /**
     * @param FilepathMethods $filepathMethods
     * @return Method[]
     *
     * @throws NodeTypeNotConvertable
     * @throws StringsException
     */
    public function buildMultipleFromFilepathMethods(FilepathMethods $filepathMethods): array
    {
        return array_map(function (Function_|ClassMethod $function) use ($filepathMethods) {
            $filepath = $filepathMethods->getFilepath();
            $codePositionRange = $this->codePositionRangeFactory->byClassMethodOrFunction($function);

            return Method::create(
                MethodSignature::create(
                    $this->paramTypesExtractor->extractFromParamsList($function->params),
                    $this->returnTypeExtractor->extractFromClassMethodOrFunction($function),
                ),
                $function->name->name,
                $filepath,
                $codePositionRange,
                $this->readMethodContent($filepath, $codePositionRange),
            );
        }, $filepathMethods->getMethods());
    }

    private function readMethodContent(string $filepath, CodePositionRange $codePositionRange): string
    {
        $fileContent = $this->fileSystem->readFile($filepath);

        return substr($fileContent, $codePositionRange->getStart()->getFilePos(), $codePositionRange->getEnd()->getFilePos());
    }
}