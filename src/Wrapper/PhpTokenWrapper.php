<?php

declare(strict_types=1);

namespace App\Wrapper;

use PhpToken;

class PhpTokenWrapper
{
    /** @return PhpToken[] */
    public function tokenize(string $code): array
    {
        return PhpToken::tokenize($code);
    }
}