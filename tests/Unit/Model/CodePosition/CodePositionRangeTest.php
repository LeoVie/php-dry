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

    /** @dataProvider toStringProvider */
    public function testToString(string $expected, CodePositionRange $codePositionRange): void
    {
        self::assertSame($expected, $codePositionRange->__toString());
    }

    public function toStringProvider(): Generator
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

    /** @dataProvider countOfLinesProvider */
    public function testCountOfLines(int $expected, CodePositionRange $codePositionRange): void
    {
        self::assertSame($expected, $codePositionRange->countOfLines());
    }

    public function countOfLinesProvider(): array
    {
        return [
            '0 lines' => [
                'expected' => 0,
                'codePositionRange' => CodePositionRange::create(
                    CodePosition::create(100, 10),
                    CodePosition::create(100, 30),
                ),
            ],
            'multiple lines' => [
                'expected' => 890,
                'codePositionRange' => CodePositionRange::create(
                    CodePosition::create(100, 10),
                    CodePosition::create(990, 30),
                ),
            ],
        ];
    }
}