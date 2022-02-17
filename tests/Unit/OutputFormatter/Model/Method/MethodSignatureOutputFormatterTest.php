<?php

declare(strict_types=1);

namespace App\Tests\Unit\OutputFormatter\Model\Method;

use App\Model\Method\MethodSignature;
use App\OutputFormatter\Model\Method\MethodSignatureOutputFormatter;
use PHPUnit\Framework\TestCase;

class MethodSignatureOutputFormatterTest extends TestCase
{
    /** @dataProvider formatProvider */
    public function testToString(string $expected, MethodSignature $method): void
    {
        $methodSignatureOutput = new MethodSignatureOutputFormatter();

        self::assertSame($expected, $methodSignatureOutput->format($method));
    }

    public function formatProvider(): \Generator
    {
        yield [
            '(): int',
            MethodSignature::create([], 'int')
        ];

        yield [
            '(): ?array',
            MethodSignature::create([], '?array')
        ];

        yield [
            '(int): int',
            MethodSignature::create(['int'], 'int')
        ];

        yield [
            '(int, string): int',
            MethodSignature::create(['int', 'string'], 'int')
        ];
    }
}