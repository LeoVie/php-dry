<?php

declare(strict_types=1);

namespace App\Tokenize\Normalize;

use PhpToken;

class ConstantEncapsedStringNormalizer implements TokenNormalizer
{
    public function supports(PhpToken $token): bool
    {
        return $token->id === T_CONSTANT_ENCAPSED_STRING;
    }

    public function reset(): self
    {
        return $this;
    }

    public function normalizeToken(PhpToken $token): PhpToken
    {
        return new PhpToken(T_CONSTANT_ENCAPSED_STRING, 'string', $token->line, $token->pos);
    }
}