<?php

declare(strict_types=1);

namespace App\Tests\Unit\Util\Subsequence;

use App\Exception\SubsequenceUtilNotFound;
use App\Util\Subsequence\LongestCommonSubsequenceUtil;
use App\Util\Subsequence\SimilarTextSubsequenceUtil;
use App\Util\Subsequence\SubsequenceUtil;
use App\Util\Subsequence\SubsequenceUtilPicker;
use PHPUnit\Framework\TestCase;

class SubsequenceUtilPickerTest extends TestCase
{
    /** @dataProvider pickProvider */
    public function testPick(SubsequenceUtil $expected, SubsequenceUtilPicker $subsequenceUtilPicker, string $strategy): void
    {
        self::assertSame($expected, $subsequenceUtilPicker->pick($strategy));
    }

    public function pickProvider(): array
    {
        $longestCommonSubsequenceUtil = $this->createMock(LongestCommonSubsequenceUtil::class);
        $similarTextSubsequenceUtil = $this->createMock(SimilarTextSubsequenceUtil::class);

        $subsequenceUtilPicker = new SubsequenceUtilPicker(
            $longestCommonSubsequenceUtil,
            $similarTextSubsequenceUtil
        );

        return [
            'LCS' => [
                'expected' => $longestCommonSubsequenceUtil,
                'subsequenceUtilPicker' => $subsequenceUtilPicker,
                'strategy' => SubsequenceUtilPicker::STRATEGY_LCS,
            ],
            'similar_text' => [
                'expected' => $similarTextSubsequenceUtil,
                'subsequenceUtilPicker' => $subsequenceUtilPicker,
                'strategy' => SubsequenceUtilPicker::STRATEGY_SIMILAR_TEXT,
            ],
        ];
    }

    public function testPickThrows(): void
    {
        self::expectException(SubsequenceUtilNotFound::class);

        (new SubsequenceUtilPicker(
            $this->createMock(LongestCommonSubsequenceUtil::class),
            $this->createMock(SimilarTextSubsequenceUtil::class)
        ))->pick('not-existing');
    }
}
