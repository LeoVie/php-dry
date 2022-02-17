<?php

declare(strict_types=1);

namespace App\Tests\Unit\OutputFormatter\Model\CodePosition;

use App\Model\CodePosition\CodePosition;
use App\OutputFormatter\Model\CodePosition\CodePositionOutputFormatter;
use PHPUnit\Framework\TestCase;

class CodePositionOutputFormatterTest extends TestCase
{
    /** @dataProvider formatProvider */
    public function testFormat(string $expected, CodePosition $codePosition): void
    {
        $codePositionOutput = new CodePositionOutputFormatter();

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