<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\FilepathMethods;

use App\Model\FilepathMethods\FilepathMethods;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPUnit\Framework\TestCase;

class FilepathMethodsTest extends TestCase
{
    /** @dataProvider getFilepathProvider */
    public function testGetFilepath(string $expected, FilepathMethods $filepathMethods): void
    {
        self::assertSame($expected, $filepathMethods->getFilepath());
    }

    public function getFilepathProvider(): \Generator
    {
        $filepath = '/var/www/foo.php';
        yield [$filepath, FilepathMethods::create($filepath, [])];

        $filepath = '/foo/bar/bla/Test.php';
        yield [$filepath, FilepathMethods::create($filepath, [])];
    }

    /** @dataProvider getMethodsProvider */
    public function testGetMethods(array $expected, FilepathMethods $filepathMethods): void
    {
        self::assertSame($expected, $filepathMethods->getMethods());
    }

    public function getMethodsProvider(): \Generator
    {
        $methods = [];
        yield 'no methods' => [$methods, FilepathMethods::create('', $methods)];

        $methods = [
            $this->createMock(ClassMethod::class),
            $this->createMock(ClassMethod::class),
            $this->createMock(ClassMethod::class),
        ];
        yield 'classMethods' => [$methods, FilepathMethods::create('', $methods)];

        $methods = [
            $this->createMock(Function_::class),
            $this->createMock(Function_::class),
            $this->createMock(Function_::class),
        ];
        yield 'functions' => [$methods, FilepathMethods::create('', $methods)];

        $methods = [
            $this->createMock(Function_::class),
            $this->createMock(ClassMethod::class),
            $this->createMock(ClassMethod::class),
            $this->createMock(Function_::class),
        ];
        yield 'mixed classMethods and functions' => [$methods, FilepathMethods::create('', $methods)];
    }
}
