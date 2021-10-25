<?php

declare(strict_types=1);

namespace App\Tests\Unit\Grouper;

use PHPUnit\Framework\TestCase;

class NormalizedTokenSequenceRepresentativesBySimilarityGrouperTest extends TestCase
{
    public function testFoo(): void
    {
        self::markTestSkipped();
    }

//    /** @dataProvider groupProvider */
//    public function testGroup(array $expected, array $normalizedTokenSequenceRepresentatives, Configuration $configuration): void
//    {
//        self::assertSame
//        ($expected,
//            (new NormalizedTokenSequenceRepresentativesBySimilarityGrouper(new LongestCommonSubsequenceAnalyzer(), new ArrayUtil()))
//                ->group($normalizedTokenSequenceRepresentatives, $configuration)
//        );
//    }
//
//    public function groupProvider(): \Generator
//    {
//        yield 'empty' => [
//            'expected' => [],
//            'normalizedTokenSequenceRepresentatives' => [],
//            'configuration' => Configuration::create('', 0, 2, 0),
//        ];
//
//        $normalizedTokenSequenceRepresentatives = [
//            NormalizedTokenSequenceRepresentative::create(
//                TokenSequence::create([]),
//                MethodsCollection::create($this->createMock(Method::class))
//            ),
//        ];
//        yield 'only one normalizedTokenSequenceRepresentative' => [
//            'expected' => [$normalizedTokenSequenceRepresentatives],
//            'normalizedTokenSequenceRepresentatives' => $normalizedTokenSequenceRepresentatives,
//            'configuration' => Configuration::create('', 0, 2, 0),
//        ];
//
//        $normalizedTokenSequenceRepresentatives = [
//            NormalizedTokenSequenceRepresentative::create(
//                TokenSequence::create([]),
//                MethodsCollection::create($this->createMock(Method::class))
//            ),
//            NormalizedTokenSequenceRepresentative::create(
//                TokenSequence::create([
//                    new \PhpToken(T_VARIABLE, '$x1'),
//                ]),
//                MethodsCollection::create($this->createMock(Method::class))
//            ),
//        ];
//        yield 'only dissimilar normalizedTokenSequenceRepresentatives' => [
//            'expected' => [
//                [$normalizedTokenSequenceRepresentatives[0]],
//                [$normalizedTokenSequenceRepresentatives[1]],
//            ],
//            'normalizedTokenSequenceRepresentatives' => $normalizedTokenSequenceRepresentatives,
//            'configuration' => Configuration::create('', 0, 2, 0),
//        ];
//
//        $normalizedTokenSequenceRepresentatives = [
//            NormalizedTokenSequenceRepresentative::create(
//                TokenSequence::create([
//                    new \PhpToken(T_VARIABLE, '$x1'),
//                    new \PhpToken(T_VARIABLE, '$x2'),
//                    new \PhpToken(T_VARIABLE, '$x3'),
//                ]),
//                MethodsCollection::create($this->createMock(Method::class))
//            ),
//            NormalizedTokenSequenceRepresentative::create(
//                TokenSequence::create([
//                    new \PhpToken(T_VARIABLE, '$x1'),
//                    new \PhpToken(T_VARIABLE, '$x2'),
//                    new \PhpToken(T_VARIABLE, '$x3'),
//                ]),
//                MethodsCollection::create($this->createMock(Method::class))
//            ),
//        ];
//        yield 'only similar normalizedTokenSequenceRepresentatives' => [
//            'expected' => [
//                $normalizedTokenSequenceRepresentatives
//            ],
//            'normalizedTokenSequenceRepresentatives' => $normalizedTokenSequenceRepresentatives,
//            'configuration' => Configuration::create('', 0, 2, 0),
//        ];
//    }
}