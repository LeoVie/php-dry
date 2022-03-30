<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory\SourceCloneCandidate;

use App\Collection\MethodsCollection;
use App\Factory\SourceCloneCandidate\Type2SourceCloneCandidateFactory;
use App\Merge\Type2SourceCloneCandidatesMerger;
use App\Model\Method\Method;
use App\Model\SourceCloneCandidate\Type2SourceCloneCandidate;
use App\Model\SourceCloneCandidate\Type1SourceCloneCandidate;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use LeoVie\PhpTokenNormalize\Service\TokenSequenceNormalizer;
use PHPUnit\Framework\TestCase;

class Type2SourceCloneCandidateFactoryTest extends TestCase
{
    /** @dataProvider createMultipleProvider */
    public function testCreateMultiple(
        array                   $expected,
        TokenSequenceNormalizer $tokenSequenceNormalizer,
        array                   $sourceCloneCandidates,
    ): void {
        $type2SourceCloneCandidatesMerger = $this->createMock(Type2SourceCloneCandidatesMerger::class);
        $type2SourceCloneCandidatesMerger->method('merge')->willReturnArgument(0);

        self::assertEquals(
            $expected,
            (new Type2SourceCloneCandidateFactory($tokenSequenceNormalizer, $type2SourceCloneCandidatesMerger))
                ->createMultiple($sourceCloneCandidates)
        );
    }

    public function createMultipleProvider(): \Generator
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

        $type1SourceCloneCandidates = [
            Type1SourceCloneCandidate::create(
                $tokenSequences[0],
                $methodsCollections[0]
            ),
            Type1SourceCloneCandidate::create(
                $tokenSequences[1],
                $methodsCollections[1]
            ),
        ];

        $normalizedTokenSequences = [
            TokenSequence::create([]),
            TokenSequence::create([$this->createMock(\PhpToken::class)]),
        ];

        $expected = [
            Type2SourceCloneCandidate::create(
                $normalizedTokenSequences[0],
                $methodsCollections[0]
            ),
            Type2SourceCloneCandidate::create(
                $normalizedTokenSequences[1],
                $methodsCollections[1]
            ),
        ];

        $tokenSequenceNormalizer = $this->createMock(TokenSequenceNormalizer::class);
        $tokenSequenceNormalizer->method('normalizeLevel2')->willReturnOnConsecutiveCalls(...$normalizedTokenSequences);

        yield [
            $expected,
            $tokenSequenceNormalizer,
            $type1SourceCloneCandidates,
        ];
    }
}
