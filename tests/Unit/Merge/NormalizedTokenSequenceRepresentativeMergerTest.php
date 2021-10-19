<?php

declare(strict_types=1);

namespace App\Tests\Unit\Merge;

use App\Collection\MethodsCollection;
use App\Merge\NormalizedTokenSequenceRepresentativeMerger;
use App\Model\Method\Method;
use App\Model\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentative;
use App\Tokenize\TokenSequence;
use PHPUnit\Framework\TestCase;

class NormalizedTokenSequenceRepresentativeMergerTest extends TestCase
{
    /** @dataProvider mergeProvider */
    public function testMerge(array $expected, array $normalizedTokenSequenceRepresentatives): void
    {
        self::assertEquals($expected, (new NormalizedTokenSequenceRepresentativeMerger())->merge($normalizedTokenSequenceRepresentatives));
    }

    public function mergeProvider(): \Generator
    {
        yield 'empty' => [
            'expected' => [],
            'normalizedTokenSequenceRepresentatives' => [],
        ];

        $tokenSequence1 = TokenSequence::create([
            new \PhpToken(T_VARIABLE, '$x1'),
        ]);
        $tokenSequence2 = TokenSequence::create([
            new \PhpToken(T_LNUMBER, '1'),
        ]);
        $method1 = $this->createMock(Method::class);
        $method2 = $this->createMock(Method::class);
        $method3 = $this->createMock(Method::class);
        $method4 = $this->createMock(Method::class);
        $method5 = $this->createMock(Method::class);
        $method6 = $this->createMock(Method::class);

        yield 'not empty' => [
            'expected' => [
                NormalizedTokenSequenceRepresentative::create(
                    $tokenSequence1,
                    MethodsCollection::create($method1, $method2, $method4, $method5)
                ),
                NormalizedTokenSequenceRepresentative::create(
                    $tokenSequence2,
                    MethodsCollection::create($method3, $method6)
                ),
            ],
            'normalizedTokenSequenceRepresentatives' => [
                NormalizedTokenSequenceRepresentative::create(
                    $tokenSequence1,
                    MethodsCollection::create($method1, $method2)
                ),
                NormalizedTokenSequenceRepresentative::create(
                    $tokenSequence2,
                    MethodsCollection::create($method3)
                ),
                NormalizedTokenSequenceRepresentative::create(
                    $tokenSequence1,
                    MethodsCollection::create($method4, $method5)
                ),
                NormalizedTokenSequenceRepresentative::create(
                    $tokenSequence2,
                    MethodsCollection::create($method6)
                ),
            ],
        ];
    }
}