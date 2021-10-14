<?php

declare(strict_types=1);

namespace App\Tokenize\Normalize;

use PhpToken;

class NothingToNormalizeNormalizer implements TokenNormalizer
{
    public static function getDefaultPriority(): int
    {
        return PHP_INT_MIN;
    }

    public function supports(PhpToken $token): bool
    {
        return true;
    }

    public function reset(): self
    {
        return $this;
    }

    public function normalizeToken(PhpToken $token): PhpToken
    {
        return new PhpToken($token->id, $token->text, $token->line, $token->pos);
    }
}