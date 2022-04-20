<?php

declare(strict_types=1);

namespace App\Factory;

use App\Cache\TokenSequenceCache;
use App\Model\Method\Method;
use App\Wrapper\PhpTokenWrapper;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;

class TokenSequenceFactory
{
    public function __construct(private PhpTokenWrapper $phpTokenWrapper, private TokenSequenceCache $cache)
    {
    }

    public function createFromMethod(Method $method): TokenSequence
    {
        $cachedTokenSequence = $this->cache->get($method);
        if ($cachedTokenSequence !== null) {
            return $cachedTokenSequence;
        }

        $tokenSequence = $this->create('<?php ' . $method->getContent());
        $this->cache->store($method, $tokenSequence);

        return $tokenSequence;
    }

    private function create(string $code): TokenSequence
    {
        return TokenSequence::create($this->phpTokenWrapper->tokenize($code));
    }
}
