<?php

declare(strict_types=1);

namespace App\Tests\Unit\Util\Subsequence;

use App\Util\Subsequence\SimilarTextSubsequenceUtil;
use PHPUnit\Framework\TestCase;

class SimilarTextSubsequenceUtilTest extends TestCase
{
    /** @dataProvider percentageOfSimilarTextProvider */
    public function testPercentageOfSimilarText(int $expected, string $a, string $b): void
    {
        self::assertSame($expected, (new SimilarTextSubsequenceUtil())->percentageOfSimilarText($a, $b));
    }

    public function percentageOfSimilarTextProvider(): array
    {
        return [
            'abc <-> abc' => [
                'expected' => 100,
                'a' => 'abc',
                'b' => 'abc',
            ],
            'abc <-> def' => [
                'expected' => 0,
                'a' => 'abc',
                'b' => 'def',
            ],
            'abc <-> ab' => [
                'expected' => 67,
                'a' => 'abc',
                'b' => 'ab',
            ],
            'abc <-> cba' => [
                'expected' => 33,
                'a' => 'abc',
                'b' => 'cba',
            ],
            'abc <-> a_b_c' => [
                'expected' => 60,
                'a' => 'abc',
                'b' => 'a_b_c',
            ],
            'abc <-> empty' => [
                'expected' => 0,
                'a' => 'abc',
                'b' => '',
            ],
            'empty <-> abc' => [
                'expected' => 0,
                'a' => '',
                'b' => 'abc',
            ],
        ];
    }
}
