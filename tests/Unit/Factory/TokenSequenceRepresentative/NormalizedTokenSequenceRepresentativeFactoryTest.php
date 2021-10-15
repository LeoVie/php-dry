<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory\TokenSequenceRepresentative;

use App\Collection\MethodsCollection;
use App\Factory\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentativeFactory;
use App\Model\Method\Method;
use App\Model\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentative;
use App\Model\TokenSequenceRepresentative\TokenSequenceRepresentative;
use App\Tokenize\TokenSequence;
use App\Tokenize\TokenSequenceNormalizer;
use PHPUnit\Framework\TestCase;

class NormalizedTokenSequenceRepresentativeFactoryTest extends TestCase
{
    /** @dataProvider createMultipleByTokenSequenceRepresentativesProvider */
    public function testCreateMultipleByTokenSequenceRepresentatives(
        array                   $expected,
        TokenSequenceNormalizer $tokenSequenceNormalizer,
        array                   $tokenSequenceRepresentatives,
    ): void
    {
        self::assertEquals(
            $expected,
            (new NormalizedTokenSequenceRepresentativeFactory($tokenSequenceNormalizer))
                ->normalizeMultipleTokenSequenceRepresentatives($tokenSequenceRepresentatives)
        );
    }

    public function createMultipleByTokenSequenceRepresentativesProvider(): \Generator
    {
        $tokenSequences = [
            TokenSequence::create([$this->createMock(\PhpToken::class)]),
            TokenSequence::create([$this->createMock(\PhpToken::class), $this->createMock(\PhpToken::class)]),
        ];
        $methodsCollections = [
            MethodsCollection::withInitialContent($this->createMock(Method::class)),
            MethodsCollection::withInitialContent(
                $this->createMock(Method::class),
                $this->createMock(Method::class)
            ),
        ];

        $tokenSequenceRepresentatives = [
            TokenSequenceRepresentative::create(
                $tokenSequences[0],
                $methodsCollections[0]
            ),
            TokenSequenceRepresentative::create(
                $tokenSequences[1],
                $methodsCollections[1]
            ),
        ];

        $normalizedTokenSequences = [
            TokenSequence::create([]),
            TokenSequence::create([$this->createMock(\PhpToken::class)]),
        ];

        $expected = [
            NormalizedTokenSequenceRepresentative::create(
                $normalizedTokenSequences[0],
                $methodsCollections[0]
            ),
            NormalizedTokenSequenceRepresentative::create(
                $normalizedTokenSequences[1],
                $methodsCollections[1]
            ),
        ];

        $tokenSequenceNormalizer = $this->createMock(TokenSequenceNormalizer::class);
        $tokenSequenceNormalizer->method('normalizeLevel2')->willReturnOnConsecutiveCalls(...$normalizedTokenSequences);

        yield [
            $expected,
            $tokenSequenceNormalizer,
            $tokenSequenceRepresentatives,
        ];
    }
}