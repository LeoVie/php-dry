<?php

declare(strict_types=1);

namespace App\Tests\Unit\ModelOutput\CodePosition;

use App\Model\CodePosition\CodePosition;
use App\ModelOutput\CodePosition\CodePositionOutput;
use PHPUnit\Framework\TestCase;

class CodePositionOutputTest extends TestCase
{
    /** @dataProvider formatProvider */
    public function testFormat(string $expected, CodePosition $codePosition): void
    {
        $codePositionOutput = new CodePositionOutput();

        self::assertSame($expected, $codePositionOutput->format($codePosition));
    }

    public function formatProvider(): array
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