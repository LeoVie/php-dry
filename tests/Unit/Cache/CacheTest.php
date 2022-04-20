<?php

namespace App\Tests\Unit\Cache;

use App\Cache\Cache;
use LeoVie\PhpFilesystem\Service\Filesystem;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function testStoreAndGet(): void
    {
        $filesystem = $this->createMock(Filesystem::class);

        $cache = new Cache($filesystem);
        $cache->setCacheFilepath('');

        $cache->store('abc', 123);

        self::assertEquals(123, $cache->get('abc'));
    }
}