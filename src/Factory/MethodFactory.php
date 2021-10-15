<?php

declare(strict_types=1);

namespace App\Factory;

use App\Exception\NodeTypeNotConvertable;
use App\Factory\CodePosition\CodePositionRangeFactory;
use App\File\ReadFileContent;
use App\Model\CodePosition\CodePositionRange;
use App\Model\FilepathMethods\FilepathMethods;
use App\Model\Method\Method;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\StringsException;

class MethodFactory
{
    public function __construct(
        private CodePositionRangeFactory $codePositionRangeFactory,
        private ReadFileContent          $readFileContent,
        private MethodSignatureFactory   $methodSignatureFactory,
    )
    {
    }

    /**
     * @return Method[]
     *
     * @throws NodeTypeNotConvertable
     * @throws StringsException
     * @throws FilesystemException
     */
    public function buildMultipleFromFilepathMethods(FilepathMethods $filepathMethods): array
    {
        $filepath = $filepathMethods->getFilepath();

        return array_map(
            fn(Function_|ClassMethod $m): Method => $this->oneFromFilepath($filepath, $m),
            $filepathMethods->getMethods()
        );
    }

    /**
     * @throws NodeTypeNotConvertable
     * @throws StringsException
     * @throws FilesystemException
     */
    private function oneFromFilepath(string $filepath, ClassMethod|Function_ $method): Method
    {
        $codePositionRange = $this->codePositionRangeFactory->byClassMethodOrFunction($method);

        return Method::create(
            $this->methodSignatureFactory->create($method),
            $method->name->name,
            $filepath,
            $codePositionRange,
            $this->readMethodContent($filepath, $codePositionRange),
        );
    }

    /**
     * @throws FilesystemException
     */
    private function readMethodContent(string $filepath, CodePositionRange $codePositionRange): string
    {
        return $this->readFileContent->readPart(
            $filepath,
            $codePositionRange->getStart()->getFilePos(),
            $codePositionRange->getEnd()->getFilePos()
        );
    }
}