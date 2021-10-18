<?php

declare(strict_types=1);

namespace App\Tests\Unit\Grouper;

use App\Collection\MethodsCollection;
use App\Compare\MethodSignatureComparer;
use App\Grouper\MethodsBySignatureGrouper;
use App\Model\CodePosition\CodePositionRange;
use App\Model\Method\Method;
use App\Model\Method\MethodSignature;
use Generator;
use PHPUnit\Framework\TestCase;

class MethodsBySignatureGrouperTest extends TestCase
{
    /** @dataProvider groupProvider */
    public function testGroup(array $expected, array $methods): void
    {
        self::assertEquals($expected, (new MethodsBySignatureGrouper(new MethodSignatureComparer()))->group($methods));
    }

    public function groupProvider(): Generator
    {
        yield 'no methods' => [
            'expected' => [],
            'methods' => [],
        ];

        $method = $this->createMethod(MethodSignature::create(['int'], 'string'));
        yield 'one method' => [
            'expected' => [MethodsCollection::create($method)],
            'methods' => [$method],
        ];

        $method1 = $this->createMethod(MethodSignature::create(['int'], 'string'));
        $method2 = $this->createMethod(MethodSignature::create(['int', 'int'], 'string'));
        $method3 = $this->createMethod(MethodSignature::create(['string'], 'array'));
        yield 'only methods with different signatures' => [
            'expected' => [
                MethodsCollection::create($method1),
                MethodsCollection::create($method2),
                MethodsCollection::create($method3),
            ],
            'methods' => [$method1, $method2, $method3],
        ];

        $method1 = $this->createMethod(MethodSignature::create(['int'], 'string'));
        $method2 = $this->createMethod(MethodSignature::create(['int'], 'string'));
        $method3 = $this->createMethod(MethodSignature::create(['int'], 'string'));
        yield 'only methods with same signatures' => [
            'expected' => [
                MethodsCollection::create($method1, $method2, $method3),
            ],
            'methods' => [$method1, $method2, $method3],
        ];

        $method1 = $this->createMethod(MethodSignature::create(['int'], 'string'));
        $method2 = $this->createMethod(MethodSignature::create(['array'], 'string'));
        $method3 = $this->createMethod(MethodSignature::create(['int'], 'string'));
        $method4 = $this->createMethod(MethodSignature::create(['string', 'int'], 'string'));
        yield 'mixed' => [
            'expected' => [
                MethodsCollection::create($method1, $method3),
                MethodsCollection::create($method2),
                MethodsCollection::create($method4),
            ],
            'methods' => [$method1, $method2, $method3, $method4],
        ];
    }

    private function createMethod(MethodSignature $methodSignature): Method
    {
        return Method::create(
            $methodSignature,
            '',
            '',
            $this->createMock(CodePositionRange::class),
            ''
        );
    }
}