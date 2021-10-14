<?php

declare(strict_types=1);

namespace App\Parse\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Stmt\ClassMethod;

class ExtractClassMethodsNodeVisitor extends NodeVisitorAbstract
{
    /** @var ClassMethod[] */
    private array $methods = [];

    public function reset(): self
    {
        return new self();
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof ClassMethod) {
            $this->methods[] = $node;
        }

        return null;
    }

    /** @return ClassMethod[] */
    public function getMethods(): array
    {
        return $this->methods;
    }
}