<?php

declare(strict_types=1);

namespace App\Tests\Unit\ModelOutput\Method;

use App\Model\CodePosition\CodePosition;
use App\Model\CodePosition\CodePositionRange;
use App\Model\Method\Method;
use App\Model\Method\MethodSignature;
use App\ModelOutput\CodePosition\CodePositionOutput;
use App\ModelOutput\CodePosition\CodePositionRangeOutput;
use App\ModelOutput\Method\MethodOutput;
use PhpParser\Node\Stmt\ClassMethod;
use PHPUnit\Framework\TestCase;

class MethodOutputTest extends TestCase
{
    /** @dataProvider toStringProvider */
    public function testToString(string $expected, Method $method): void
    {
        $methodOutput = new MethodOutput(
            new CodePositionRangeOutput(
                new CodePositionOutput()
            )
        );

        self::assertSame($expected, $methodOutput->format($method));
    }

    public function toStringProvider(): \Generator
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
                $this->createMock(ClassMethod::class),
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
                $this->createMock(ClassMethod::class),
            ),
        ];
    }
}