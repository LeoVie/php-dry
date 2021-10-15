<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory;

use App\Factory\MethodSignatureFactory;
use App\Model\Method\MethodSignature;
use App\Parse\Extractor\ParamTypesExtractor;
use App\Parse\Extractor\ReturnTypeExtractor;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPUnit\Framework\TestCase;

class MethodSignatureFactoryTest extends TestCase
{
    /** @dataProvider createProvider */
    public function testCreate(
        MethodSignature       $expected,
        ClassMethod|Function_ $method,
        ReturnTypeExtractor   $returnTypeExtractor,
        ParamTypesExtractor   $paramTypesExtractor
    ): void
    {
        self::assertEquals(
            $expected,
            (new MethodSignatureFactory($returnTypeExtractor, $paramTypesExtractor))->create($method)
        );
    }

    public function createProvider(): \Generator
    {
        $paramTypes = ['int', 'string'];
        $returnType = 'array';
        $expected = MethodSignature::create($paramTypes, $returnType);

        $paramTypesExtractor = $this->createMock(ParamTypesExtractor::class);
        $paramTypesExtractor->method('extract')->willReturn($paramTypes);

        $returnTypeExtractor = $this->createMock(ReturnTypeExtractor::class);
        $returnTypeExtractor->method('extract')->willReturn($returnType);

        yield [
            $expected,
            $this->createMock(ClassMethod::class),
            $returnTypeExtractor,
            $paramTypesExtractor,
        ];
    }
}