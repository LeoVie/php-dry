<?php

declare(strict_types=1);

namespace App\Tokenize\Normalize;

use PhpToken;

class DNumberNormalizer implements TokenNormalizer
{
    public function supports(PhpToken $token): bool
    {
        return $token->id === T_DNUMBER;
    }

    public function reset(): self
    {
        return $this;
    }

    public function normalizeToken(PhpToken $token): PhpToken
    {
        return new PhpToken(T_DNUMBER, '1.0', $token->line, $token->pos);
    }
}