<?php

declare(strict_types=1);

namespace App\Tokenize\Normalize;

use PhpToken;

class LNumberNormalizer implements TokenNormalizer
{
    public function supports(PhpToken $token): bool
    {
        return $token->id === T_LNUMBER;
    }

    public function reset(): self
    {
        return $this;
    }

    public function normalizeToken(PhpToken $token): PhpToken
    {
        return new PhpToken(T_LNUMBER, '1', $token->line, $token->pos);
    }
}