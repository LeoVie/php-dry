<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Method;

use App\Model\Method\Method;
use App\Model\Method\MethodTokenSequence;
use App\Tokenize\TokenSequence;
use Generator;
use PHPUnit\Framework\TestCase;

class MethodTokenSequenceTest extends TestCase
{
    /** @dataProvider getMethodProvider */
    public function testGetMethod(Method $expected, MethodTokenSequence $methodTokenSequence): void
    {
        self::assertSame($expected, $methodTokenSequence->getMethod());
    }

    public function getMethodProvider(): Generator
    {
        $method = $this->createMock(Method::class);
        yield [$method, MethodTokenSequence::create($method, $this->createMock(TokenSequence::class))];
    }

    /** @dataProvider getTokenSequenceProvider */
    public function testGetTokenSequence(TokenSequence $expected, MethodTokenSequence $methodTokenSequence): void
    {
        self::assertSame($expected, $methodTokenSequence->getTokenSequence());
    }

    public function getTokenSequenceProvider(): Generator
    {
        $tokenSequence = $this->createMock(TokenSequence::class);
        yield [$tokenSequence, MethodTokenSequence::create($this->createMock(Method::class), $tokenSequence)];
    }
}