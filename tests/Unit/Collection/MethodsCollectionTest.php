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

    /** @dataProvider getAllProvider */
    public function testGetAll(array $expected, array $methods): void
    {
        self::assertSame($expected, MethodsCollection::create(...$methods)->getAll());
    }

    public function getAllProvider(): array
    {
        $methodA = $this->mockMethodWithIdentity('a');
        $otherMethodA = $this->mockMethodWithIdentity('a');
        $methodB = $this->mockMethodWithIdentity('b');
        $methodC = $this->mockMethodWithIdentity('c');
        return [
            'one method' => [
                'expected' => [$methodA],
                'methods' => [$methodA],
            ],
            'multiple methods (same signature)' => [
                'expected' => [
                    $methodA,
                    $otherMethodA,
                ],
                'methods' => [
                    $methodA,
                    $otherMethodA,
                ],
            ],
            'multiple methods (already sorted)' => [
                'expected' => [
                    $methodA,
                    $methodB,
                ],
                'methods' => [
                    $methodA,
                    $methodB,
                ],
            ],
            'multiple methods (unsorted)' => [
                'expected' => [
                    $methodA,
                    $methodB,
                    $methodC,
                ],
                'methods' => [
                    $methodB,
                    $methodC,
                    $methodA,
                ],
            ],
        ];
    }

    private function mockMethodWithIdentity(string $identity): Method
    {
        $method = $this->createMock(Method::class);
        $method->method('identity')->willReturn($identity);

        return $method;
    }

    /** @dataProvider equalsProvider */
    public function testEquals(bool $expected, MethodsCollection $a, MethodsCollection $b): void
    {
        self::assertSame($expected, $a->equals($b));
    }

    public function equalsProvider(): array
    {
        $methodA = $this->mockMethodWithIdentity('a');
        $otherMethodA = $this->mockMethodWithIdentity('a');
        $methodB = $this->mockMethodWithIdentity('b');
        $methodC = $this->mockMethodWithIdentity('c');
        return [
            'one method (same)' => [
                'expected' => true,
                'a' => MethodsCollection::create($methodA),
                'b' => MethodsCollection::create($methodA),
            ],
            'one method (not equals)' => [
                'expected' => false,
                'a' => MethodsCollection::create($methodA),
                'b' => MethodsCollection::create($methodB),
            ],
            'multiple methods (same)' => [
                'expected' => true,
                'a' => MethodsCollection::create($methodA, $methodB),
                'b' => MethodsCollection::create($methodA, $methodB)
            ],
            'multiple methods (not same, but equals)' => [
                'expected' => true,
                'a' => MethodsCollection::create($methodA, $methodB),
                'b' => MethodsCollection::create($otherMethodA, $methodB)
            ],
            'multiple methods (not equals)' => [
                'expected' => false,
                'a' => MethodsCollection::create($methodA, $methodC),
                'b' => MethodsCollection::create($methodB)
            ],
            'multiple methods (not equals, B has more)' => [
                'expected' => false,
                'a' => MethodsCollection::create($methodA, $methodC),
                'b' => MethodsCollection::create($methodA, $methodC, $methodB)
            ],
        ];
    }
}