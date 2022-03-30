<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\SourceCloneCandidate;

use App\Collection\MethodsCollection;
use App\Model\SourceCloneCandidate\Type2SourceCloneCandidate;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use PHPUnit\Framework\TestCase;

class Type2SourceCloneCandidateTest extends TestCase
{
    public function testGetTokenSequence(): void
    {
        $tokenSequence = $this->createMock(TokenSequence::class);
        self::assertSame($tokenSequence, Type2SourceCloneCandidate::create(
            $tokenSequence,
            $this->createMock(MethodsCollection::class)
        )->getTokenSequence());
    }

    public function testGetMethodsCollection(): void
    {
        $methodsCollection = $this->createMock(MethodsCollection::class);
        self::assertSame($methodsCollection, Type2SourceCloneCandidate::create(
            $this->createMock(TokenSequence::class),
            $methodsCollection,
        )->getMethodsCollection());
    }

    public function testIdentity(): void
    {
        $tokenSequence = $this->createMock(TokenSequence::class);
        $tokenSequence->method('identity')->willReturn('tokenSequenceIdentity');
        self::assertSame('tokenSequenceIdentity', Type2SourceCloneCandidate::create(
            $tokenSequence,
            $this->createMock(MethodsCollection::class)
        )->identity());
    }

    public function testGroupID(): void
    {
        $tokenSequence = $this->createMock(TokenSequence::class);
        $tokenSequence->method('identity')->willReturn('tokenSequenceIdentity');
        self::assertSame('tokenSequenceIdentity', Type2SourceCloneCandidate::create(
            $tokenSequence,
            $this->createMock(MethodsCollection::class)
        )->groupID());
    }

    public function testToString(): void
    {
        $tokenSequence = $this->createMock(TokenSequence::class);
        $tokenSequence->method('identity')->willReturn('tokenSequenceIdentity');
        self::assertSame('tokenSequenceIdentity', Type2SourceCloneCandidate::create(
            $tokenSequence,
            $this->createMock(MethodsCollection::class)
        )->__toString());
    }
}
