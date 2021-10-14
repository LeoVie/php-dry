<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\CodePosition;

use App\Model\CodePosition\CodePosition;
use App\Model\CodePosition\CodePositionRange;
use Generator;
use PHPUnit\Framework\TestCase;

class CodePositionRangeTest extends TestCase
{
    /** @dataProvider getStartProvider */
    public function testGetStart(CodePosition $expected, CodePositionRange $codePositionRange): void
    {
        self::assertSame($expected, $codePositionRange->getStart());
    }

    public function getStartProvider(): Generator
    {
        $start = CodePosition::create(10, 15);
        yield [
            'expected' => $start,
            'codePositionRange' => CodePositionRange::create($start, CodePosition::create(11, 15)),
        ];

        $start = CodePosition::create(700, 16);
        yield [
            'expected' => $start,
            'codePositionRange' => CodePositionRange::create($start, CodePosition::create(1100, 15)),
        ];
    }

    /** @dataProvider getEndProvider */
    public function testGetEnd(CodePosition $expected, CodePositionRange $codePositionRange): void
    {
        self::assertSame($expected, $codePositionRange->getEnd());
    }

    public function getEndProvider(): Generator
    {
        $end = CodePosition::create(10, 15);
        yield [
            'expected' => $end,
            'codePositionRange' => CodePositionRange::create(CodePosition::create(1, 15), $end),
        ];

        $end = CodePosition::create(700, 16);
        yield [
            'expected' => $end,
            'codePositionRange' => CodePositionRange::create(CodePosition::create(11, 15), $end),
        ];
    }
}