<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory\TokenSequenceRepresentative;

use App\Collection\MethodsCollection;
use App\Factory\TokenSequenceRepresentative\Type2TokenSequenceRepresentativeFactory;
use App\Merge\Type2TokenSequenceRepresentativeMerger;
use App\Model\Method\Method;
use App\Model\TokenSequenceRepresentative\Type2TokenSequenceRepresentative;
use App\Model\TokenSequenceRepresentative\Type1TokenSequenceRepresentative;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use LeoVie\PhpTokenNormalize\Service\TokenSequenceNormalizer;
use PHPUnit\Framework\TestCase;

class Type2TokenSequenceRepresentativeFactoryTest extends TestCase
{
    /** @dataProvider createMultipleByTokenSequenceRepresentativesProvider */
    public function testCreateMultipleByTokenSequenceRepresentatives(
        array                   $expected,
        TokenSequenceNormalizer $tokenSequenceNormalizer,
        array                   $tokenSequenceRepresentatives,
    ): void
    {
        $type2TokenSequenceRepresentativeMerger = $this->createMock(Type2TokenSequenceRepresentativeMerger::class);
        $type2TokenSequenceRepresentativeMerger->method('merge')->willReturnArgument(0);

        self::assertEquals(
            $expected,
            (new Type2TokenSequenceRepresentativeFactory($tokenSequenceNormalizer, $type2TokenSequenceRepresentativeMerger))
                ->createMultiple($tokenSequenceRepresentatives)
        );
    }

    public function createMultipleByTokenSequenceRepresentativesProvider(): \Generator
    {
        $tokenSequences = [
            TokenSequence::create([$this->createMock(\PhpToken::class)]),
            TokenSequence::create([$this->createMock(\PhpToken::class), $this->createMock(\PhpToken::class)]),
        ];
        $methodsCollections = [
            MethodsCollection::create($this->createMock(Method::class)),
            MethodsCollection::create(
                $this->createMock(Method::class),
                $this->createMock(Method::class)
            ),
        ];

        $type1TokenSequenceRepresentatives = [
            Type1TokenSequenceRepresentative::create(
                $tokenSequences[0],
                $methodsCollections[0]
            ),
            Type1TokenSequenceRepresentative::create(
                $tokenSequences[1],
                $methodsCollections[1]
            ),
        ];

        $normalizedTokenSequences = [
            TokenSequence::create([]),
            TokenSequence::create([$this->createMock(\PhpToken::class)]),
        ];

        $expected = [
            Type2TokenSequenceRepresentative::create(
                $normalizedTokenSequences[0],
                $methodsCollections[0]
            ),
            Type2TokenSequenceRepresentative::create(
                $normalizedTokenSequences[1],
                $methodsCollections[1]
            ),
        ];

        $tokenSequenceNormalizer = $this->createMock(TokenSequenceNormalizer::class);
        $tokenSequenceNormalizer->method('normalizeLevel2')->willReturnOnConsecutiveCalls(...$normalizedTokenSequences);

        yield [
            $expected,
            $tokenSequenceNormalizer,
            $type1TokenSequenceRepresentatives,
        ];
    }
}