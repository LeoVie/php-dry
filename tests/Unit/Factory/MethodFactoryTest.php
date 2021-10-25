<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory;

use App\Factory\CodePosition\CodePositionRangeFactory;
use App\Factory\MethodFactory;
use App\Factory\MethodSignatureFactory;
use App\Model\CodePosition\CodePositionRange;
use App\Model\FilepathMethods\FilepathMethods;
use App\Model\Method\Method;
use App\Model\Method\MethodSignature;
use LeoVie\PhpFilesystem\Service\Filesystem;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PHPUnit\Framework\TestCase;

class MethodFactoryTest extends TestCase
{
    /** @dataProvider buildMultipleFromFilepathMethodsProvider */
    public function testBuildMultipleFromFilepathMethod(
        array                    $expected,
        FilepathMethods          $filepathMethods,
        CodePositionRangeFactory $codePositionRangeFactory,
        Filesystem               $filesystem,
        MethodSignatureFactory   $methodSignatureFactory,
    ): void
    {
        self::assertEquals(
            $expected,
            (new MethodFactory($codePositionRangeFactory, $filesystem, $methodSignatureFactory))
                ->buildMultipleFromFilepathMethods($filepathMethods)
        );
    }

    public function buildMultipleFromFilepathMethodsProvider(): \Generator
    {
        $methodSignatures = [
            $this->createMock(MethodSignature::class),
            $this->createMock(MethodSignature::class),
            $this->createMock(MethodSignature::class),
        ];
        $methodNames = ['firstMethod', 'secondMethod', 'thirdMethod'];
        $filepath = 'fp1';
        $codePositionRanges = [
            $this->createMock(CodePositionRange::class),
            $this->createMock(CodePositionRange::class),
            $this->createMock(CodePositionRange::class),
        ];
        $methodContents = ['c1', 'c2', 'c3'];

        $expected = [];
        for ($i = 0; $i < count($methodSignatures); $i++) {
            $expected[] = Method::create(
                $methodSignatures[$i],
                $methodNames[$i],
                $filepath,
                $codePositionRanges[$i],
                $methodContents[$i],
            );
        }

        $methods = array_map(function (string $name): ClassMethod {
            $classMethod = $this->createMock(ClassMethod::class);
            $classMethod->name = $this->createMock(Identifier::class);
            $classMethod->name->name = $name;

            return $classMethod;
        }, $methodNames);

        $filepathMethods = FilepathMethods::create($filepath, $methods);

        $codePositionRangeFactory = $this->createMock(CodePositionRangeFactory::class);
        $codePositionRangeFactory->method('byClassMethodOrFunction')->willReturnOnConsecutiveCalls(...$codePositionRanges);

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('readFilePart')->willReturnOnConsecutiveCalls(...$methodContents);

        $methodSignatureFactory = $this->createMock(MethodSignatureFactory::class);
        $methodSignatureFactory->method('create')->willReturnOnConsecutiveCalls(...$methodSignatures);

        yield [
            'expected' => $expected,
            $filepathMethods,
            $codePositionRangeFactory,
            $filesystem,
            $methodSignatureFactory,
        ];
    }
}