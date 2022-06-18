<?php

declare(strict_types=1);

namespace App\Model\Method;

use App\Model\CodePosition\CodePositionRange;
use App\Model\Identity;

class Method implements Identity, \JsonSerializable
{
    private function __construct(
        private MethodSignature   $methodSignature,
        private string            $name,
        private string            $filepath,
        private CodePositionRange $codePositionRange,
        private string            $content,
        private string            $classFQN,
    )
    {
    }

    public static function create(
        MethodSignature   $methodSignature,
        string            $name,
        string            $filepath,
        CodePositionRange $codePositionRange,
        string            $content,
        string            $classFQN,
    ): self
    {
        return new self($methodSignature, $name, $filepath, $codePositionRange, $content, $classFQN);
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

    public function getClassFQN(): string
    {
        return $this->classFQN;
    }

    public function identity(): string
    {
        return $this->getFilepath()
            . '_'
            . $this->getName()
            . '_'
            . $this->getCodePositionRange()->getStart()->getLine()
            . $this->getCodePositionRange()->getStart()->getFilePos();
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
