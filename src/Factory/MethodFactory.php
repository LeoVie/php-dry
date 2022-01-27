<?php

declare(strict_types=1);

namespace App\Factory;

use App\Factory\CodePosition\CodePositionRangeFactory;
use App\Model\CodePosition\CodePositionRange;
use App\Model\FilepathMethods\FilepathMethods;
use App\Model\Method\Method;
use LeoVie\PhpFilesystem\Exception\InvalidBoundaries;
use LeoVie\PhpFilesystem\Model\Boundaries;
use LeoVie\PhpFilesystem\Service\Filesystem;
use LeoVie\PhpMethodsParser\Exception\NodeTypeNotConvertable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\PcreException;
use Safe\Exceptions\StringsException;

class MethodFactory
{
    public function __construct(
        private CodePositionRangeFactory $codePositionRangeFactory,
        private Filesystem               $filesystem,
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
     * @throws InvalidBoundaries
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
     * @throws InvalidBoundaries
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
            $method
        );
    }

    /**
     * @throws FilesystemException
     * @throws StringsException
     * @throws InvalidBoundaries
     * @throws PcreException
     */
    private function readMethodContent(string $filepath, CodePositionRange $codePositionRange): string
    {
        $methodContent = $this->filesystem->readFilePart(
            $filepath,
            Boundaries::create(
                $codePositionRange->getStart()->getFilePos(),
                $codePositionRange->getEnd()->getFilePos() + 1
            )
        );

        return $this->indentLinesCorrectly($methodContent);
    }

    /** @throws PcreException */
    private function indentLinesCorrectly(string $methodContent): string
    {
        $lines = explode("\n", $methodContent);
        $correctlyIndentedLines = [array_shift($lines)];
        foreach ($lines as $line) {
            /** @var string $correctlyIndentedLine */
            $correctlyIndentedLine = \Safe\preg_replace("@^ {4}@", '', $line);
            $correctlyIndentedLines[] = $correctlyIndentedLine;
        }

        return join("\n", $correctlyIndentedLines);
    }
}