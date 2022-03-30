<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Method;

use App\Model\Method\Method;
use App\Model\Method\MethodTokenSequence;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use PHPUnit\Framework\TestCase;

class MethodTokenSequenceTest extends TestCase
{
    public function testGetMethod(): void
    {
        $method = $this->createMock(Method::class);
        self::assertSame(
            $method,
            MethodTokenSequence::create(
                $method,
                $this->createMock(TokenSequence::class)
            )->getMethod()
        );
    }

    public function testGetTokenSequence(): void
    {
        $tokenSequence = $this->createMock(TokenSequence::class);
        self::assertSame(
            $tokenSequence,
            MethodTokenSequence::create(
                $this->createMock(Method::class),
                $tokenSequence
            )->getTokenSequence()
        );
    }

    public function testIdentity(): void
    {
        $tokenSequence = $this->createMock(TokenSequence::class);
        $tokenSequence->method('identity')->willReturn('token sequence identity');

        self::assertSame(
            'token sequence identity',
            MethodTokenSequence::create(
                $this->createMock(Method::class),
                $tokenSequence
            )->identity()
        );
    }
}
