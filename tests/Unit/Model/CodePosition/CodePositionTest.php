<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\CodePosition;

use App\Model\CodePosition\CodePosition;
use PHPUnit\Framework\TestCase;

class CodePositionTest extends TestCase
{
    /** @dataProvider getLineProvider */
    public function testGetLine(int $expected, CodePosition $codePosition): void
    {
        self::assertSame($expected, $codePosition->getLine());
    }

    public function getLineProvider(): array
    {
        return [
            [
                'expected' => 10,
                CodePosition::create(10, 15),
            ],
            [
                'expected' => 17,
                CodePosition::create(17, 15),
            ],
        ];
    }

    /** @dataProvider getFilePosProvider */
    public function testGetFilePos(int $expected, CodePosition $codePosition): void
    {
        self::assertSame($expected, $codePosition->getFilePos());
    }

    public function getFilePosProvider(): array
    {
        return [
            [
                'expected' => 15,
                CodePosition::create(10, 15),
            ],
            [
                'expected' => 29,
                CodePosition::create(10, 29),
            ],
        ];
    }

    /** @dataProvider toStringProvider */
    public function testToString(string $expected, CodePosition $codePosition): void
    {
        self::assertSame($expected, $codePosition->__toString());
    }

    public function toStringProvider(): array
    {
        return [
            [
                'expected' => '10 (position 15)',
                CodePosition::create(10, 15),
            ],
            [
                'expected' => '999 (position 29)',
                CodePosition::create(999, 29),
            ],
        ];
    }
}