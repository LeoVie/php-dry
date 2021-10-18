<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Method;

use App\Model\CodePosition\CodePositionRange;
use App\Model\Method\Method;
use App\Model\Method\MethodSignature;
use Generator;
use PHPUnit\Framework\TestCase;

class MethodTest extends TestCase
{
    /** @dataProvider getMethodSignatureProvider */
    public function testGetMethodSignature(MethodSignature $expected, Method $method): void
    {
        self::assertSame($expected, $method->getMethodSignature());
    }

    public function getMethodSignatureProvider(): Generator
    {
        $methodSignature = $this->createMock(MethodSignature::class);
        yield [$methodSignature, Method::create(
            $methodSignature,
            '',
            '',
            $this->createMock(CodePositionRange::class),
            ''
        )];
    }

    /** @dataProvider getNameProvider */
    public function testGetName(string $expected, Method $method): void
    {
        self::assertSame($expected, $method->getName());
    }

    public function getNameProvider(): Generator
    {
        $name = 'foo';
        yield [$name, Method::create(
            $this->createMock(MethodSignature::class),
            $name,
            '',
            $this->createMock(CodePositionRange::class),
            ''
        )];

        $name = 'bar';
        yield [$name, Method::create(
            $this->createMock(MethodSignature::class),
            $name,
            '',
            $this->createMock(CodePositionRange::class),
            ''
        )];
    }

    /** @dataProvider getFilepathProvider */
    public function testGetFilepath(string $expected, Method $method): void
    {
        self::assertSame($expected, $method->getFilepath());
    }

    public function getFilepathProvider(): Generator
    {
        $filepath = '/var/www/foo.php';
        yield [$filepath, Method::create(
            $this->createMock(MethodSignature::class),
            '',
            $filepath,
            $this->createMock(CodePositionRange::class),
            ''
        )];

        $filepath = '/var/www/bar.php';
        yield [$filepath, Method::create(
            $this->createMock(MethodSignature::class),
            '',
            $filepath,
            $this->createMock(CodePositionRange::class),
            ''
        )];
    }

    /** @dataProvider getCodePositionRangeProvider */
    public function testGetCodePositionRange(CodePositionRange $expected, Method $method): void
    {
        self::assertSame($expected, $method->getCodePositionRange());
    }

    public function getCodePositionRangeProvider(): Generator
    {
        $codePositionRange = $this->createMock(CodePositionRange::class);
        yield [$codePositionRange, Method::create(
            $this->createMock(MethodSignature::class),
            '',
            '',
            $codePositionRange,
            ''
        )];
    }

    /** @dataProvider getContentProvider */
    public function testGetContent(string $expected, Method $method): void
    {
        self::assertSame($expected, $method->getContent());
    }

    public function getContentProvider(): Generator
    {
        $content = 'foobar bla';
        yield [$content, Method::create(
            $this->createMock(MethodSignature::class),
            '',
            '',
            $this->createMock(CodePositionRange::class),
            $content
        )];

        $content = 'bla bla bla';
        yield [$content, Method::create(
            $this->createMock(MethodSignature::class),
            '',
            '',
            $this->createMock(CodePositionRange::class),
            $content
        )];
    }

    /** @dataProvider toStringProvider */
    public function testToString(string $expected, Method $method): void
    {
        self::assertSame($expected, $method->__toString());
    }

    public function toStringProvider(): Generator
    {
        $codePositionRange = $this->createMock(CodePositionRange::class);
        $codePositionRange->method('__toString')->willReturn('code position range');
        yield [
            'expected' => '/var/www/foo.php: foobar (code position range)',
            'method' => Method::create(
                $this->createMock(MethodSignature::class),
                'foobar',
                '/var/www/foo.php',
                $codePositionRange,
                ''
            )
        ];

        $codePositionRange = $this->createMock(CodePositionRange::class);
        $codePositionRange->method('__toString')->willReturn('code position range 2');
        yield [
            'expected' => '/fp/bar.php: barfoo (code position range 2)',
            'method' => Method::create(
                $this->createMock(MethodSignature::class),
                'barfoo',
                '/fp/bar.php',
                $codePositionRange,
                ''
            )
        ];
    }
}