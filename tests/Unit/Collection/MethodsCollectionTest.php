<?php

declare(strict_types=1);

namespace App\Tests\Unit\Collection;

use App\Collection\MethodsCollection;
use App\Exception\CollectionCannotBeEmpty;
use App\Model\Method\Method;
use App\Model\Method\MethodSignature;
use PHPUnit\Framework\TestCase;

/** @group now */
class MethodsCollectionTest extends TestCase
{
    /** @dataProvider createProvider */
    public function testCreate(array $methods): void
    {
        self::assertSame($methods, MethodsCollection::create(...$methods)->getAll());
    }

    public function createProvider(): \Generator
    {
        yield 'one method' => [
            'methods' => [$this->createMock(Method::class)],
        ];
        yield 'multiple methods' => [
            'methods' => [
                $this->createMock(Method::class),
                $this->createMock(Method::class),
            ],
        ];
    }

    public function testCreateThrowsWhenCalledWithNoMethods(): void
    {
        self::expectException(CollectionCannotBeEmpty::class);

        MethodsCollection::create();
    }

    /** @dataProvider getFirstProvider */
    public function testGetFirst(Method $expected, MethodsCollection $methodsCollection): void
    {
        self::assertSame($expected, $methodsCollection->getFirst());
    }

    public function getFirstProvider(): \Generator
    {
        $methods = [
            $this->createMock(Method::class),
            $this->createMock(Method::class),
        ];

        yield [
            'expected' => $methods[0],
            'methodsCollection' => MethodsCollection::create(...$methods),
        ];
    }

    /** @dataProvider extractParamTypesProvider */
    public function testExtractParamTypes(array $expected, MethodsCollection $methodsCollection): void
    {
        self::assertSame($expected, $methodsCollection->extractParamTypes());
    }

    public function extractParamTypesProvider(): \Generator
    {
        $paramTypes = ['int', 'string'];
        $method = $this->mockMethodWithParamTypes($paramTypes);
        yield 'one method' => [
            'expected' => $paramTypes,
            'methodsCollection' => MethodsCollection::create($method),
        ];

        $paramTypes = ['int', 'string'];
        $methods = [
            $this->mockMethodWithParamTypes($paramTypes),
            $this->mockMethodWithParamTypes($paramTypes),
        ];
        yield 'multiple methods' => [
            'expected' => $paramTypes,
            'methodsCollection' => MethodsCollection::create($methods[0]),
        ];
    }

    private function mockMethodWithParamTypes(array $paramTypes): Method
    {
        $method = $this->createMock(Method::class);
        $methodSignature = $this->createMock(MethodSignature::class);
        $methodSignature->method('getParamTypes')->willReturn($paramTypes);
        $method->method('getMethodSignature')->willReturn($methodSignature);

        return $method;
    }
}