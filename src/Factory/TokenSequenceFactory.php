<?php

declare(strict_types=1);

namespace App\Factory;

use App\Model\Method\Method;
use App\Wrapper\PhpTokenWrapper;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;

class TokenSequenceFactory
{
    public function __construct(private PhpTokenWrapper $phpTokenWrapper)
    {
    }

    public function createFromMethod(Method $method): TokenSequence
    {
        return $this->create('<?php ' . $method->getContent());
    }

    private function create(string $code): TokenSequence
    {
        return TokenSequence::create($this->phpTokenWrapper->tokenize($code));
    }
}
