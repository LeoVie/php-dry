<?php

namespace App\Cache;

use App\Model\Method\Method;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;

class TokenSequenceCache
{
    private const CACHE_FILE = '.php-dry-token-sequence-cache';

    /** @param Cache<TokenSequence> $cache */
    public function __construct(private Cache $cache)
    {
    }

    public function get(Method $method): ?TokenSequence
    {
        $this->cache->setCacheFilepath(self::CACHE_FILE);
        return $this->cache->get($method->identity());
    }

    public function store(Method $method, TokenSequence $tokenSequence): void
    {
        $this->cache->store($method->identity(), $tokenSequence);
    }
}