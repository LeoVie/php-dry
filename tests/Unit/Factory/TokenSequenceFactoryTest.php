<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory;

use App\Factory\TokenSequenceFactory;
use App\Tokenize\TokenSequence;
use App\Wrapper\PhpTokenWrapper;
use Generator;
use PHPUnit\Framework\TestCase;

class TokenSequenceFactoryTest extends TestCase
{
    /** @dataProvider createProvider */
    public function testCreate(TokenSequence $expected, PhpTokenWrapper $phpTokenWrapper): void
    {
        self::assertEquals($expected, (new TokenSequenceFactory($phpTokenWrapper))->create(''));
    }

    public function createProvider(): Generator
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