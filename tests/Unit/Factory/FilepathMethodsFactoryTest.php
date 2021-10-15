<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory;

use App\Factory\FilepathMethodsFactory;
use App\Model\FilepathMethods\FilepathMethods;
use App\Parse\Parser\MethodsParser;
use PhpParser\Node\Stmt\ClassMethod;
use PHPUnit\Framework\TestCase;

class FilepathMethodsFactoryTest extends TestCase
{
    /** @dataProvider createProvider */
    public function testCreate(array $expected, array $filepaths, MethodsParser $methodsParser): void
    {
        self::assertEquals($expected, (new FilepathMethodsFactory($methodsParser))->create($filepaths));
    }

    public function createProvider(): \Generator
    {
        $methodsParser = $this->createMock(MethodsParser::class);
        $filepaths = [];
        yield 'no filepath' => [
            'expected' => [],
            'filepaths' => $filepaths,
            'methodsParser' => $methodsParser,
        ];

        $methodsParser = $this->createMock(MethodsParser::class);
        $methods = [$this->createMock(ClassMethod::class)];
        $methodsParser->method('extractMethods')->willReturn($methods);
        $filepaths = ['fp1'];
        yield 'one filepath' => [
            'expected' => [
                FilepathMethods::create($filepaths[0], $methods),
            ],
            'filepaths' => $filepaths,
            'methodsParser' => $methodsParser,
        ];

        $methodsParser = $this->createMock(MethodsParser::class);
        $methods = [$this->createMock(ClassMethod::class)];
        $methodsParser->method('extractMethods')->willReturn($methods);
        $filepaths = ['fp1', 'fp2'];
        yield 'multiple filepaths' => [
            'expected' => [
                FilepathMethods::create($filepaths[0], $methods),
                FilepathMethods::create($filepaths[1], $methods),
            ],
            'filepaths' => $filepaths,
            'methodsParser' => $methodsParser,
        ];
    }
}