<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\TokenSequenceRepresentative;

use App\Collection\MethodsCollection;
use App\Model\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentative;
use App\Tokenize\TokenSequence;
use PHPUnit\Framework\TestCase;

class NormalizedTokenSequenceRepresentativeTest extends TestCase
{
    public function testGetTokenSequence(): void
    {
        $tokenSequence = $this->createMock(TokenSequence::class);
        self::assertSame($tokenSequence, NormalizedTokenSequenceRepresentative::create(
            $tokenSequence,
            $this->createMock(MethodsCollection::class)
        )->getTokenSequence());
    }

    public function testGetMethodsCollection(): void
    {
        $methodsCollection = $this->createMock(MethodsCollection::class);
        self::assertSame($methodsCollection, NormalizedTokenSequenceRepresentative::create(
            $this->createMock(TokenSequence::class),
            $methodsCollection,
        )->getMethodsCollection());
    }
}