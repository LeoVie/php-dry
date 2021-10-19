<?php

declare(strict_types=1);

namespace App\Tests\Unit\Tokenize\Analyze;

use App\Tokenize\Analyze\LongestCommonSubsequenceAnalyzer;
use App\Tokenize\TokenSequence;
use PHPUnit\Framework\TestCase;

class LongestCommonSubsequenceAnalyzerTest extends TestCase
{
    /** @dataProvider findProvider */
    public function testFind(int $expected, TokenSequence $a, TokenSequence $b): void
    {
        self::assertSame($expected, (new LongestCommonSubsequenceAnalyzer())->find($a, $b));
    }

    public function findProvider(): array
    {
        return [
            'both are empty' => [
                'expected' => 0,
                'a' => TokenSequence::create([]),
                'b' => TokenSequence::create([]),
            ],
            'a is empty' => [
                'expected' => 0,
                'a' => TokenSequence::create([]),
                'b' => TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$a'),
                ]),
            ],
            'b is empty' => [
                'expected' => 0,
                'a' => TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$a'),
                ]),
                'b' => TokenSequence::create([]),
            ],
            'both same' => [
                'expected' => 3,
                'a' => TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$a'),
                ]),
                'b' => TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$a'),
                ]),
            ],
            'both not exactly same (#1)' => [
                'expected' => 2,
                'a' => TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$a'),
                ]),
                'b' => TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$b'),
                    new \PhpToken(T_VARIABLE, '$a'),
                ]),
            ],
            'both not exactly same (#2)' => [
                'expected' => 3,
                'a' => TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$a'),
                ]),
                'b' => TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$b'),
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$a'),
                ]),
            ],
            'both not exactly same (#3)' => [
                'expected' => 4,
                'a' => TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$b'),
                    new \PhpToken(T_VARIABLE, '$c'),
                    new \PhpToken(T_VARIABLE, '$d'),
                ]),
                'b' => TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$b'),
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$c'),
                    new \PhpToken(T_VARIABLE, '$d'),
                ]),
            ],
        ];
    }
}