<?php

declare(strict_types=1);

namespace App\Parse\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;

class ExtractMethodsNodeVisitor extends NodeVisitorAbstract
{
    /** @var array<ClassMethod|Function_> */
    private array $methods = [];

    public function reset(): self
    {
        return new self();
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof ClassMethod || $node instanceof Function_) {
            $this->methods[] = $node;
        }

        return null;
    }

    /** @return array<ClassMethod|Function_> */
    public function getMethods(): array
    {
        return $this->methods;
    }
}