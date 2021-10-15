<?php

declare(strict_types=1);

namespace App\Tests\Unit\Collection;

use App\Collection\MethodsCollection;
use App\Model\Method\Method;
use App\Model\Method\MethodSignature;
use PHPUnit\Framework\TestCase;

class MethodsCollectionTest extends TestCase
{
    public function testEmpty(): void
    {
        self::assertEmpty(MethodsCollection::empty()->getAll());
    }

    /** @dataProvider withInitialContentProvider */
    public function testWithInitialContent(array $methods): void
    {
        self::assertSame($methods, MethodsCollection::withInitialContent(...$methods)->getAll());
    }

    public function withInitialContentProvider(): \Generator
    {
        yield 'empty' => [
            'methods' => [],
        ];
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

    public function testGetFirstOnEmpty(): void
    {
        self::assertNull(MethodsCollection::empty()->getFirst());
    }

    /** @dataProvider getFirstOnNonEmptyProvider */
    public function testGetFirstOnNonEmpty(Method $expected, MethodsCollection $methodsCollection): void
    {
        self::assertSame($expected, $methodsCollection->getFirst());
    }

    public function getFirstOnNonEmptyProvider(): \Generator
    {
        $methods = [
            $this->createMock(Method::class),
            $this->createMock(Method::class),
        ];

        yield [
            'expected' => $methods[0],
            'methodsCollection' => MethodsCollection::withInitialContent(...$methods),
        ];
    }

    /** @dataProvider extractParamTypesProvider */
    public function testExtractParamTypes(array $expected, MethodsCollection $methodsCollection): void
    {
        self::assertSame($expected, $methodsCollection->extractParamTypes());
    }

    public function extractParamTypesProvider(): \Generator
    {
        yield 'empty' => [
            'expected' => [],
            'methodsCollection' => MethodsCollection::empty(),
        ];

        $paramTypes = ['int', 'string'];
        $method = $this->mockMethodWithParamTypes($paramTypes);
        yield 'one method' => [
            'expected' => $paramTypes,
            'methodsCollection' => MethodsCollection::withInitialContent($method),
        ];

        $paramTypes = ['int', 'string'];
        $methods = [
            $this->mockMethodWithParamTypes($paramTypes),
            $this->mockMethodWithParamTypes($paramTypes),
        ];
        yield 'multiple methods' => [
            'expected' => $paramTypes,
            'methodsCollection' => MethodsCollection::withInitialContent($methods[0]),
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