<?php

namespace App\Cache;

use App\Model\Method\Method;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;

class MethodTokenSequenceCache
{
    private const CACHE_FILE = __DIR__ . '/.php-dry-method-token-sequence-cache';

    public function __construct(private Cache $cache)
    {
        $this->cache->setCacheFilepath(self::CACHE_FILE);
    }

    public function get(Method $method): ?TokenSequence
    {
        return $this->cache->get($method->identity());
    }

    public function store(Method $method, TokenSequence $tokenSequence): void
    {
        $this->cache->store($method->identity(), $tokenSequence);
    }
}