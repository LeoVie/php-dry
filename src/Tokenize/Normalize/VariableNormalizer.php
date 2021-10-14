<?php

declare(strict_types=1);

namespace App\Tokenize\Normalize;

use App\Tokenize\ReplacementRegister;
use PhpToken;

class VariableNormalizer implements TokenNormalizer
{
    private ReplacementRegister $variableReplacementRegister;

    public function supports(PhpToken $token): bool
    {
        return $token->id === T_VARIABLE;
    }

    public function reset(): self
    {
        $this->variableReplacementRegister = ReplacementRegister::create('$x');

        return $this;
    }

    public function normalizeToken(PhpToken $token): PhpToken
    {
        $originalVariable = $token->text;
        if (!$this->variableReplacementRegister->isReplacementRegistered($originalVariable)) {
            $this->variableReplacementRegister->register($originalVariable);
        }
        $normalizedVariable = $this->variableReplacementRegister->getReplacement($originalVariable);

        return new PhpToken(T_VARIABLE, $normalizedVariable, $token->line, $token->pos);
    }
}