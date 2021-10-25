<?php

declare(strict_types=1);

namespace App\Tests\Unit\TokenAnalyze;

use App\ServiceFactory\LcsSolverForPhpTokensFactory;
use App\TokenAnalyze\LongestCommonSubsequenceAnalyzer;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use PHPUnit\Framework\TestCase;

class LongestCommonSubsequenceAnalyzerTest extends TestCase
{
    /** @dataProvider findProvider */
    public function testFind(TokenSequence $expected, TokenSequence $a, TokenSequence $b): void
    {
        self::assertEquals($expected, (new LongestCommonSubsequenceAnalyzer((new LcsSolverForPhpTokensFactory())->create()))->find($a, $b));
    }

    public function findProvider(): array
    {
        return [
            'both are empty' => [
                'expected' => TokenSequence::create([]),
                'a' => TokenSequence::create([]),
                'b' => TokenSequence::create([]),
            ],
            'a is empty' => [
                'expected' => TokenSequence::create([]),
                'a' => TokenSequence::create([]),
                'b' => TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$a'),
                ]),
            ],
            'b is empty' => [
                'expected' => TokenSequence::create([]),
                'a' => TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$a'),
                ]),
                'b' => TokenSequence::create([]),
            ],
            'both same' => [
                'expected' => TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$a'),
                ]),
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
                'expected' => TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$a'),
                ]),
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
                'expected' => TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$a'),
                ]),
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
                'expected' => TokenSequence::create([
                    new \PhpToken(T_VARIABLE, '$a'),
                    new \PhpToken(T_VARIABLE, '$b'),
                    new \PhpToken(T_VARIABLE, '$c'),
                    new \PhpToken(T_VARIABLE, '$d'),
                ]),
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