<?php

declare(strict_types=1);

namespace App\Tests\Unit\ModelOutput\CodePosition;

use App\Model\CodePosition\CodePosition;
use App\Model\CodePosition\CodePositionRange;
use App\ModelOutput\CodePosition\CodePositionOutput;
use App\ModelOutput\CodePosition\CodePositionRangeOutput;
use PHPUnit\Framework\TestCase;

class CodePositionRangeOutputTest extends TestCase
{
    /** @dataProvider formatProvider */
    public function testFormat(string $expected, CodePositionRange $codePositionRange): void
    {
        $codePositionRangeOutput = new CodePositionRangeOutput(
            new CodePositionOutput()
        );

        self::assertSame($expected, $codePositionRangeOutput->format($codePositionRange));
    }

    public function formatProvider(): \Generator
    {
        $start = CodePosition::create(10, 15);
        $end = CodePosition::create(11, 15);
        yield [
            'expected' => '10 (position 15) - 11 (position 15) (1 lines)',
            'codePositionRange' => CodePositionRange::create($start, $end),
        ];

        $start = CodePosition::create(700, 16);
        $end = CodePosition::create(1100, 15);
        yield [
            'expected' => '700 (position 16) - 1100 (position 15) (400 lines)',
            'codePositionRange' => CodePositionRange::create($start, $end),
        ];
    }
}