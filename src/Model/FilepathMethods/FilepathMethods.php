<?php

declare(strict_types=1);

namespace App\Model\FilepathMethods;

use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;

/** @psalm-immutable */
class FilepathMethods
{
    /** @param Function_[]|ClassMethod[] $methods */
    public static function create(string $filepath, array $methods): FilepathMethods
    {
        return new self($filepath, $methods);
    }

    /** @param Function_[]|ClassMethod[] $methods */
    private function __construct(private string $filepath, private array $methods)
    {
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    /** @return Function_[]|ClassMethod[] */
    public function getMethods(): array
    {
        return $this->methods;
    }
}
