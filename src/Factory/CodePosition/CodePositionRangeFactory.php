<?php

declare(strict_types=1);

namespace App\Factory\CodePosition;

use App\Model\CodePosition\CodePositionRange;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;

class CodePositionRangeFactory
{
    public function __construct(private CodePositionFactory $codePositionFactory)
    {
    }

    public function byClassMethodOrFunction(ClassMethod|Function_ $function): CodePositionRange
    {
        return CodePositionRange::create(
            $this->codePositionFactory->byStartClassMethodOrFunction($function),
            $this->codePositionFactory->byEndClassMethodOrFunction($function),
        );
    }
}