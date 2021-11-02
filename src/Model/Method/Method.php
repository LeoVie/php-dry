<?php

declare(strict_types=1);

namespace App\Model\Method;

use App\Model\CodePosition\CodePositionRange;
use App\Model\Identity;
use Safe\Exceptions\StringsException;
use Stringable;

class Method implements Stringable, Identity
{
    private function __construct(
        private MethodSignature   $methodSignature,
        private string            $name,
        private string            $filepath,
        private CodePositionRange $codePositionRange,
        private string            $content,
    )
    {
    }

    public static function create(
        MethodSignature   $methodSignature,
        string            $name,
        string            $filepath,
        CodePositionRange $codePositionRange,
        string            $content,
    ): self
    {
        return new self($methodSignature, $name, $filepath, $codePositionRange, $content);
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

    public function identity(): string
    {
        return $this->__toString();
    }

    /** @throws StringsException */
    public function __toString(): string
    {
        return \Safe\sprintf(
            '%s: %s (%s)',
            $this->getFilepath(),
            $this->getName(),
            $this->getCodePositionRange()->__toString()
        );
    }
}