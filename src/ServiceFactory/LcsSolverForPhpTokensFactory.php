<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use Eloquent\Lcs\LcsSolver;

final class LcsSolverForPhpTokensFactory
{
    public static function create(): LcsSolver
    {
        return new LcsSolver(fn(\PhpToken $l, \PhpToken $r): bool => $l->__toString() == $r->__toString());
    }
}