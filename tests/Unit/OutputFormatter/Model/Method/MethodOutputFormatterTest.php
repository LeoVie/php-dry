<?php

declare(strict_types=1);

namespace App\Tests\Unit\OutputFormatter\Model\Method;

use App\Model\CodePosition\CodePosition;
use App\Model\CodePosition\CodePositionRange;
use App\Model\Method\Method;
use App\Model\Method\MethodSignature;
use App\OutputFormatter\Model\CodePosition\CodePositionOutputFormatter;
use App\OutputFormatter\Model\CodePosition\CodePositionRangeOutputFormatter;
use App\OutputFormatter\Model\Method\MethodOutputFormatter;
use PHPUnit\Framework\TestCase;

class MethodOutputFormatterTest extends TestCase
{
    /** @dataProvider formatProvider */
    public function testFormat(string $expected, Method $method): void
    {
        $methodOutput = new MethodOutputFormatter(
            new CodePositionRangeOutputFormatter(
                new CodePositionOutputFormatter()
            )
        );

        self::assertSame($expected, $methodOutput->format($method));
    }

    public function formatProvider(): \Generator
    {
        $codePositionRange = CodePositionRange::create(
            CodePosition::create(1, 10),
            CodePosition::create(20, 17)
        );
        yield [
            'expected' => '/var/www/foo.php: foobar (1 (position 10) - 20 (position 17) (19 lines))',
            'method' => Method::create(
                $this->createMock(MethodSignature::class),
                'foobar',
                '/var/www/foo.php',
                $codePositionRange,
                '',
                ''
            ),
        ];

        yield [
            'expected' => '/fp/bar.php: barfoo (1 (position 10) - 20 (position 17) (19 lines))',
            'method' => Method::create(
                $this->createMock(MethodSignature::class),
                'barfoo',
                '/fp/bar.php',
                $codePositionRange,
                '',
                ''
            ),
        ];
    }
}
