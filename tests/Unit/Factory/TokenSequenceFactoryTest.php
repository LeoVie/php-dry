<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory;

use App\Cache\MethodTokenSequenceCache;
use App\Factory\TokenSequenceFactory;
use App\Model\Method\Method;
use App\Wrapper\PhpTokenWrapper;
use Generator;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use PHPUnit\Framework\TestCase;

class TokenSequenceFactoryTest extends TestCase
{
    /** @dataProvider createFromMethodProvider */
    public function testCreateFromMethod(TokenSequence $expected, PhpTokenWrapper $phpTokenWrapper): void
    {
        $cache = $this->createMock(MethodTokenSequenceCache::class);
        $cache->method('get')->willReturn(null);

        $method = $this->createMock(Method::class);
        $method->method('getContent')->willReturn('');
        self::assertEquals($expected, (new TokenSequenceFactory($phpTokenWrapper, $cache))->createFromMethod($method));
    }

    public function createFromMethodProvider(): Generator
    {
        $tokens = [];
        $expected = TokenSequence::create($tokens);
        $phpTokenWrapper = $this->createMock(PhpTokenWrapper::class);
        $phpTokenWrapper->method('tokenize')->willReturn($tokens);
        yield [$expected, $phpTokenWrapper];

        $tokens = [
            $this->createMock(\PhpToken::class),
            $this->createMock(\PhpToken::class),
        ];
        $expected = TokenSequence::create($tokens);
        $phpTokenWrapper = $this->createMock(PhpTokenWrapper::class);
        $phpTokenWrapper->method('tokenize')->willReturn($tokens);
        yield [$expected, $phpTokenWrapper];
    }
}
