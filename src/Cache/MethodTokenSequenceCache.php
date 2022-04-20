<?php

namespace App\Cache;

use App\Model\Method\Method;
use App\Model\Method\MethodTokenSequence;

class MethodTokenSequenceCache
{
    private const CACHE_FILE = '.php-dry-method-token-sequence-cache';

    /** @param Cache<MethodTokenSequence> $cache */
    public function __construct(private Cache $cache)
    {
    }

    public function get(Method $method): ?MethodTokenSequence
    {
        $this->cache->setCacheFilepath(self::CACHE_FILE);
        return $this->cache->get($method->identity());
    }

    public function store(Method $method, MethodTokenSequence $methodTokenSequence): void
    {
        $this->cache->store($method->identity(), $methodTokenSequence);
    }
}