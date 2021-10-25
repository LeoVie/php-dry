<?php

declare(strict_types=1);

namespace App\Tests\Unit\Merge;

use App\Collection\MethodsCollection;
use App\Merge\Type2TokenSequenceRepresentativeMerger;
use App\Model\Method\Method;
use App\Model\TokenSequenceRepresentative\Type2TokenSequenceRepresentative;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use PHPUnit\Framework\TestCase;

class Type2TokenSequenceRepresentativeMergerTest extends TestCase
{
    /** @dataProvider mergeProvider */
    public function testMerge(array $expected, array $type2TokenSequenceRepresentatives): void
    {
        self::assertEquals($expected, (new Type2TokenSequenceRepresentativeMerger())->merge($type2TokenSequenceRepresentatives));
    }

    public function mergeProvider(): \Generator
    {
        yield 'empty' => [
            'expected' => [],
            'type2TokenSequenceRepresentatives' => [],
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
                Type2TokenSequenceRepresentative::create(
                    $tokenSequence1,
                    MethodsCollection::create($method1, $method2, $method4, $method5)
                ),
                Type2TokenSequenceRepresentative::create(
                    $tokenSequence2,
                    MethodsCollection::create($method3, $method6)
                ),
            ],
            'type2TokenSequenceRepresentatives' => [
                Type2TokenSequenceRepresentative::create(
                    $tokenSequence1,
                    MethodsCollection::create($method1, $method2)
                ),
                Type2TokenSequenceRepresentative::create(
                    $tokenSequence2,
                    MethodsCollection::create($method3)
                ),
                Type2TokenSequenceRepresentative::create(
                    $tokenSequence1,
                    MethodsCollection::create($method4, $method5)
                ),
                Type2TokenSequenceRepresentative::create(
                    $tokenSequence2,
                    MethodsCollection::create($method6)
                ),
            ],
        ];
    }
}