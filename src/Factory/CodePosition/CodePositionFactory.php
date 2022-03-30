<?php

declare(strict_types=1);

namespace App\Factory\CodePosition;

use App\Model\CodePosition\CodePosition;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;

class CodePositionFactory
{
    public function byStartClassMethodOrFunction(ClassMethod|Function_ $function): CodePosition
    {
        return CodePosition::create($function->getStartLine(), $function->getStartFilePos());
    }

    public function byEndClassMethodOrFunction(ClassMethod|Function_ $function): CodePosition
    {
        return CodePosition::create($function->getEndLine(), $function->getEndFilePos());
    }
}
