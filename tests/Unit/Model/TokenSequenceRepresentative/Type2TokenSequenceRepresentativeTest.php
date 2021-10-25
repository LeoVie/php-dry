<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\TokenSequenceRepresentative;

use App\Collection\MethodsCollection;
use App\Model\TokenSequenceRepresentative\Type2TokenSequenceRepresentative;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use PHPUnit\Framework\TestCase;

class Type2TokenSequenceRepresentativeTest extends TestCase
{
    public function testGetTokenSequence(): void
    {
        $tokenSequence = $this->createMock(TokenSequence::class);
        self::assertSame($tokenSequence, Type2TokenSequenceRepresentative::create(
            $tokenSequence,
            $this->createMock(MethodsCollection::class)
        )->getTokenSequence());
    }

    public function testGetMethodsCollection(): void
    {
        $methodsCollection = $this->createMock(MethodsCollection::class);
        self::assertSame($methodsCollection, Type2TokenSequenceRepresentative::create(
            $this->createMock(TokenSequence::class),
            $methodsCollection,
        )->getMethodsCollection());
    }

    public function testIdentity(): void
    {
        $tokenSequence = $this->createMock(TokenSequence::class);
        $tokenSequence->method('identity')->willReturn('tokenSequenceIdentity');
        self::assertSame('tokenSequenceIdentity', Type2TokenSequenceRepresentative::create(
            $tokenSequence,
            $this->createMock(MethodsCollection::class)
        )->identity());
    }

    public function testGroupID(): void
    {
        $tokenSequence = $this->createMock(TokenSequence::class);
        $tokenSequence->method('identity')->willReturn('tokenSequenceIdentity');
        self::assertSame('tokenSequenceIdentity', Type2TokenSequenceRepresentative::create(
            $tokenSequence,
            $this->createMock(MethodsCollection::class)
        )->groupID());
    }

    public function testToString(): void
    {
        $tokenSequence = $this->createMock(TokenSequence::class);
        $tokenSequence->method('identity')->willReturn('tokenSequenceIdentity');
        self::assertSame('tokenSequenceIdentity', Type2TokenSequenceRepresentative::create(
            $tokenSequence,
            $this->createMock(MethodsCollection::class)
        )->__toString());
    }
}