<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\SourceCloneCandidate;

use App\Collection\MethodsCollection;
use App\Model\SourceCloneCandidate\Type3SourceCloneCandidate;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use PHPUnit\Framework\TestCase;

class Type3SourceCloneCandidateTest extends TestCase
{
    public function testGetTokenSequences(): void
    {
        $tokenSequences = [
            $this->createMock(TokenSequence::class),
            $this->createMock(TokenSequence::class),
            $this->createMock(TokenSequence::class),
        ];
        self::assertSame($tokenSequences, Type3SourceCloneCandidate::create(
            $tokenSequences,
            $this->createMock(MethodsCollection::class)
        )->getTokenSequences());
    }

    public function testGetMethodsCollection(): void
    {
        $methodsCollection = $this->createMock(MethodsCollection::class);
        self::assertSame($methodsCollection, Type3SourceCloneCandidate::create(
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
        self::assertSame('a-b-c', Type3SourceCloneCandidate::create(
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
        self::assertSame('a-b-c', Type3SourceCloneCandidate::create(
            $tokenSequences,
            $this->createMock(MethodsCollection::class)
        )->__toString());
    }
}