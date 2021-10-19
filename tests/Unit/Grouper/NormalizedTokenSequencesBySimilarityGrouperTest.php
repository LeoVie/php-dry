<?php

declare(strict_types=1);

namespace App\Tests\Unit\Grouper;

use App\Collection\MethodsCollection;
use App\Grouper\NormalizedTokenSequencesBySimilarityGrouper;
use App\Model\Method\Method;
use App\Model\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentative;
use App\Tokenize\Analyze\LongestCommonSubsequenceAnalyzer;
use App\Tokenize\TokenSequence;
use App\Util\ArrayUtil;
use PHPUnit\Framework\TestCase;

class NormalizedTokenSequencesBySimilarityGrouperTest extends TestCase
{
    /** @dataProvider groupProvider */
    public function testGroup(array $expected, array $normalizedTokenSequenceRepresentatives, int $minSimilarTokens): void
    {
        self::assertSame
        ($expected,
            (new NormalizedTokenSequencesBySimilarityGrouper(new LongestCommonSubsequenceAnalyzer(), new ArrayUtil()))
                ->groupSimilarNormalizedTokenSequenceRepresentatives($normalizedTokenSequenceRepresentatives, $minSimilarTokens)
        );
    }

    public function groupProvider(): \Generator
    {
        yield 'empty' => [
            'expected' => [],
            'normalizedTokenSequenceRepresentatives' => [],
            'minSimilarTokens' => 2,
        ];

        $normalizedTokenSequenceRepresentatives = [
            NormalizedTokenSequenceRepresentative::create(
                TokenSequence::create([]),
                MethodsCollection::create($this->createMock(Method::class))
            ),
        ];
        yield 'only one normalizedTokenSequenceRepresentative' => [
            'expected' => [$normalizedTokenSequenceRepresentatives],
            'normalizedTokenSequenceRepresentatives' => $normalizedTokenSequenceRepresentatives,
            'minSimilarTokens' => 2,
        ];

        $normalizedTokenSequenceRepresentatives = [
            NormalizedTokenSequenceRepresentative::create(
                TokenSequence::create([]),
                MethodsCollection::create($this->createMock(Method::class))
            ),
            NormalizedTokenSequenceRepresentative::create(
                TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$x1'),
                ]),
                MethodsCollection::create($this->createMock(Method::class))
            ),
        ];
        yield 'only dissimilar normalizedTokenSequenceRepresentatives' => [
            'expected' => [
                [$normalizedTokenSequenceRepresentatives[0]],
                [$normalizedTokenSequenceRepresentatives[1]],
            ],
            'normalizedTokenSequenceRepresentatives' => $normalizedTokenSequenceRepresentatives,
            'minSimilarTokens' => 2,
        ];

        $normalizedTokenSequenceRepresentatives = [
            NormalizedTokenSequenceRepresentative::create(
                TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$x1'),
                    new \PhpToken(T_VARIABLE, '$x2'),
                    new \PhpToken(T_VARIABLE, '$x3'),
                ]),
                MethodsCollection::create($this->createMock(Method::class))
            ),
            NormalizedTokenSequenceRepresentative::create(
                TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$x1'),
                    new \PhpToken(T_VARIABLE, '$x2'),
                    new \PhpToken(T_VARIABLE, '$x3'),
                ]),
                MethodsCollection::create($this->createMock(Method::class))
            ),
        ];
        yield 'only similar normalizedTokenSequenceRepresentatives' => [
            'expected' => [
                $normalizedTokenSequenceRepresentatives
            ],
            'normalizedTokenSequenceRepresentatives' => $normalizedTokenSequenceRepresentatives,
            'minSimilarTokens' => 2,
        ];
    }
}