<?php

declare(strict_types=1);

namespace App\Factory;

use App\Wrapper\PhpTokenWrapper;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;

class TokenSequenceFactory
{
    public function __construct(private PhpTokenWrapper $phpTokenWrapper)
    {
    }

    public function create(string $code): TokenSequence
    {
        return TokenSequence::create($this->phpTokenWrapper->tokenize($code));
    }
}