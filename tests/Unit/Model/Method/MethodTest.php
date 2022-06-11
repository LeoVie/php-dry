<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Method;

use App\Model\CodePosition\CodePosition;
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
            '',
            '',
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
            '',
            '',
        )];

        $name = 'bar';
        yield [$name, Method::create(
            $this->createMock(MethodSignature::class),
            $name,
            '',
            $this->createMock(CodePositionRange::class),
            '',
            '',
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
            '',
            '',
        )];

        $filepath = '/var/www/bar.php';
        yield [$filepath, Method::create(
            $this->createMock(MethodSignature::class),
            '',
            $filepath,
            $this->createMock(CodePositionRange::class),
            '',
            '',
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
            '',
            '',
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
            $content,
            '',
        )];

        $content = 'bla bla bla';
        yield [$content, Method::create(
            $this->createMock(MethodSignature::class),
            '',
            '',
            $this->createMock(CodePositionRange::class),
            $content,
            '',
        )];
    }

    /** @dataProvider getProjectPathProvider */
    public function testGetProjectPath(string $expected, Method $method): void
    {
        self::assertSame($expected, $method->getProjectPath());
    }

    public function getProjectPathProvider(): Generator
    {
        $projectPath = '/var/www';
        yield [$projectPath, Method::create(
            $this->createMock(MethodSignature::class),
            '',
            '',
            $this->createMock(CodePositionRange::class),
            '',
            $projectPath,
        )];

        $projectPath = 'foo/bar/bla/';
        yield [$projectPath, Method::create(
            $this->createMock(MethodSignature::class),
            '',
            '',
            $this->createMock(CodePositionRange::class),
            '',
            $projectPath,
        )];
    }

    /** @dataProvider jsonSerializeProvider */
    public function testJsonSerialize(string $expected, Method $method): void
    {
        self::assertJsonStringEqualsJsonString($expected, \Safe\json_encode($method));
    }

    public function jsonSerializeProvider(): Generator
    {
        $codePositionRange = CodePositionRange::create(
            CodePosition::create(1, 10),
            CodePosition::create(3, 10),
        );
        yield [
            'expected' => \Safe\json_encode([
                'filepath' => '/var/www/foo.php',
                'name' => 'foobar',
                'codePositionRange' => $codePositionRange,
            ]),
            'method' => Method::create(
                $this->createMock(MethodSignature::class),
                'foobar',
                '/var/www/foo.php',
                $codePositionRange,
                '',
                '',
            ),
        ];

        $codePositionRange = CodePositionRange::create(
            CodePosition::create(1, 10),
            CodePosition::create(3, 10),
        );
        yield [
            'expected' => \Safe\json_encode([
                'filepath' => '/fp/bar.php',
                'name' => 'barfoo',
                'codePositionRange' => $codePositionRange,
            ]),
            'method' => Method::create(
                $this->createMock(MethodSignature::class),
                'barfoo',
                '/fp/bar.php',
                $codePositionRange,
                '',
                '',
            ),
        ];
    }
}
