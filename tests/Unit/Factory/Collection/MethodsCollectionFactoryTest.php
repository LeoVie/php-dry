<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory\Collection;

use App\Collection\MethodsCollection;
use App\Factory\Collection\MethodsCollectionFactory;
use App\Model\CodePosition\CodePositionRange;
use App\Model\Method\HasMethod;
use App\Model\Method\Method;
use App\Model\Method\MethodSignature;
use PHPUnit\Framework\TestCase;

class MethodsCollectionFactoryTest extends TestCase
{
    /** @dataProvider fromHasMethodsProvider */
    public function testFromHasMethods(MethodsCollection $expected, array $hasMethods): void
    {
        self::assertEquals($expected, (new MethodsCollectionFactory())->fromHasMethods($hasMethods));
    }

    public function fromHasMethodsProvider(): \Generator
    {
        $method1 = $this->mockMethod('method1');
        $method2 = $this->mockMethod('method2');
        $method3 = $this->mockMethod('method3');
        $method4 = $this->mockMethod('method4');

        $hasMethod1 = $this->mockHasMethod($method1);
        $hasMethod2 = $this->mockHasMethod($method2);
        $hasMethod3 = $this->mockHasMethod($method3);
        $hasMethod4 = $this->mockHasMethod($method4);

        yield [
            'expected' => MethodsCollection::create($method1, $method2, $method3, $method4),
            'hasMethods' => [
                $hasMethod1,
                $hasMethod2,
                $hasMethod3,
                $hasMethod4,
            ],
        ];
    }

    private function mockMethod(string $name): Method
    {
        return Method::create(
            $this->createMock(MethodSignature::class),
            $name,
            '',
            $this->createMock(CodePositionRange::class),
            ''
        );
    }

    private function mockHasMethod(Method $method): HasMethod
    {
        $hasMethod = $this->createMock(HasMethod::class);
        $hasMethod->method('getMethod')->willReturn($method);

        return $hasMethod;
    }
}