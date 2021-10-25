<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\TokenSequenceRepresentative;

use App\Collection\MethodsCollection;
use App\Model\TokenSequenceRepresentative\Type2TokenSequenceRepresentative;
use App\Model\TokenSequenceRepresentative\Type3TokenSequenceRepresentative;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use PHPUnit\Framework\TestCase;

class Type3TokenSequenceRepresentativeTest extends TestCase
{
    public function testGetTokenSequences(): void
    {
        $tokenSequences = [
            $this->createMock(TokenSequence::class),
            $this->createMock(TokenSequence::class),
            $this->createMock(TokenSequence::class),
        ];
        self::assertSame($tokenSequences, Type3TokenSequenceRepresentative::create(
            $tokenSequences,
            $this->createMock(MethodsCollection::class)
        )->getTokenSequences());
    }

    public function testGetMethodsCollection(): void
    {
        $methodsCollection = $this->createMock(MethodsCollection::class);
        self::assertSame($methodsCollection, Type3TokenSequenceRepresentative::create(
            [$this->createMock(TokenSequence::class)],
            $methodsCollection,
        )->getMethodsCollection());
    }

    public function testIdentity(): void
    {
        $tokenSequences = [
            $this->mockTokenSequenceWithIdentity('a'),
            $this->mockTokenSequenceWithIdentity('b'),
            $this->mockTokenSequenceWithIdentity('c'),
        ];
        self::assertSame('a-b-c', Type3TokenSequenceRepresentative::create(
            $tokenSequences,
            $this->createMock(MethodsCollection::class)
        )->identity());
    }

    private function mockTokenSequenceWithIdentity(string $identity): TokenSequence
    {
        $tokenSequence = $this->createMock(TokenSequence::class);
        $tokenSequence->method('identity')->willReturn($identity);

        return $tokenSequence;
    }

    public function testToString(): void
    {
        $tokenSequences = [
            $this->mockTokenSequenceWithIdentity('a'),
            $this->mockTokenSequenceWithIdentity('b'),
            $this->mockTokenSequenceWithIdentity('c'),
        ];
        self::assertSame('a-b-c', Type3TokenSequenceRepresentative::create(
            $tokenSequences,
            $this->createMock(MethodsCollection::class)
        )->__toString());
    }
}