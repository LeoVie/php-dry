<?php

declare(strict_types=1);

namespace App\Model\Method;

use App\Model\CodePosition\CodePositionRange;
use App\Model\Identity;
use App\ModelOutput\CodePosition\CodePositionOutput;
use App\ModelOutput\CodePosition\CodePositionRangeOutput;
use App\ModelOutput\Method\MethodOutput;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;

class Method implements Identity, \JsonSerializable
{
    private function __construct(
        private MethodSignature       $methodSignature,
        private string                $name,
        private string                $filepath,
        private CodePositionRange     $codePositionRange,
        private string                $content,
        private ClassMethod|Function_ $parsedMethod
    )
    {
    }

    public static function create(
        MethodSignature       $methodSignature,
        string                $name,
        string                $filepath,
        CodePositionRange     $codePositionRange,
        string                $content,
        ClassMethod|Function_ $parsedMethod
    ): self
    {
        return new self($methodSignature, $name, $filepath, $codePositionRange, $content, $parsedMethod);
    }

    public function getMethodSignature(): MethodSignature
    {
        return $this->methodSignature;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    public function getCodePositionRange(): CodePositionRange
    {
        return $this->codePositionRange;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getParsedMethod(): ClassMethod|Function_
    {
        return $this->parsedMethod;
    }

    public function identity(): string
    {
        // TODO fix this
        return (new MethodOutput(
            new CodePositionRangeOutput(
                new CodePositionOutput()
            )
        ))->format($this);
    }

    /** @return array{'filepath': string, 'name': string, 'codePositionRange': CodePositionRange} */
    public function jsonSerialize(): array
    {
        return [
            'filepath' => $this->getFilepath(),
            'name' => $this->getName(),
            'codePositionRange' => $this->getCodePositionRange(),
        ];
    }
}