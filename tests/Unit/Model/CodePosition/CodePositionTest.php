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

    /** @dataProvider jsonSerializeProvider */
    public function testJsonSerialize(string $expected, CodePosition $codePosition): void
    {
        self::assertJsonStringEqualsJsonString($expected, \Safe\json_encode($codePosition));
    }

    public function jsonSerializeProvider(): array
    {
        return [
            [
                'expected' => \Safe\json_encode(['line' => 10, 'filePos' => 15]),
                CodePosition::create(10, 15),
            ],
            [
                'expected' => \Safe\json_encode(['line' => 999, 'filePos' => 29]),
                CodePosition::create(999, 29),
            ],
        ];
    }
}