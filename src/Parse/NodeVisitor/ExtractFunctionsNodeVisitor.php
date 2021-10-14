<?php

declare(strict_types=1);

namespace App\Parse\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeVisitorAbstract;

class ExtractFunctionsNodeVisitor extends NodeVisitorAbstract
{
    /** @var Node\Stmt\Function_[] */
    private array $functions = [];

    public function reset(): self
    {
        return new self();
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Function_) {
            $this->functions[] = $node;
        }

        return null;
    }

    /** @return Function_[] */
    public function getFunctions(): array
    {
        return $this->functions;
    }
}