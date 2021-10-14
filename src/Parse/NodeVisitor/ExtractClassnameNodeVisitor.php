<?php

declare(strict_types=1);

namespace App\Parse\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class ExtractClassnameNodeVisitor extends NodeVisitorAbstract
{
    private ?string $classname = null;

    public function reset(): self
    {
        return new self();
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\ClassLike) {
            $name = $node->name;

            if ($name === null) {
                return null;
            }

            $this->classname = $name->toString();

            return NodeTraverser::STOP_TRAVERSAL;
        }

        return null;
    }

    public function getClassname(): ?string
    {
        return $this->classname;
    }
}