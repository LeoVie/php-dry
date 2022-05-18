<?php

declare(strict_types=1);

namespace App\Tests\Unit\Grouper;

use App\Collection\MethodsCollection;
use App\Compare\MethodSignatureComparer;
use App\Grouper\MethodsBySignatureGrouper;
use App\Model\CodePosition\CodePositionRange;
use App\Model\Method\Method;
use App\Model\Method\MethodSignature;
use App\Model\Method\MethodSignatureGroup;
use Generator;
use PhpParser\Node\Stmt\ClassMethod;
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

        $methodSignatures = [MethodSignature::create(['int'], [0], 'string')];
        $method = $this->createMethod($methodSignatures[0]);
        yield 'one method' => [
            'expected' => [MethodSignatureGroup::create($methodSignatures[0], MethodsCollection::create($method))],
            'methods' => [$method],
        ];

        $methodSignatures = [
            MethodSignature::create(['int'], [0], 'string'),
            MethodSignature::create(['int', 'int'], [0, 1], 'string'),
            MethodSignature::create(['string'], [0], 'array')
        ];
        $methods = array_map(fn (MethodSignature $ms): Method => $this->createMethod($ms), $methodSignatures);
        yield 'only methods with different signatures' => [
            'expected' => [
                MethodSignatureGroup::create($methodSignatures[0], MethodsCollection::create($methods[0])),
                MethodSignatureGroup::create($methodSignatures[1], MethodsCollection::create($methods[1])),
                MethodSignatureGroup::create($methodSignatures[2], MethodsCollection::create($methods[2])),
            ],
            'methods' => $methods,
        ];

        $methodSignatures = [MethodSignature::create(['int'], [0], 'string')];
        $methods = [];
        for ($i = 0; $i <= 2; $i++) {
            $methods[] = $this->createMethod($methodSignatures[0]);
        }
        yield 'only methods with same signatures' => [
            'expected' => [
                MethodSignatureGroup::create($methodSignatures[0], MethodsCollection::create(...$methods)),
            ],
            'methods' => $methods,
        ];

        $methodSignatures = [
            MethodSignature::create(['int'], [0], 'string'),
            MethodSignature::create(['array'], [0], 'string'),
            MethodSignature::create(['string', 'int'], [0, 1], 'string')
        ];
        $methods = [
            $this->createMethod($methodSignatures[0]),
            $this->createMethod($methodSignatures[1]),
            $this->createMethod($methodSignatures[0]),
            $this->createMethod($methodSignatures[2]),
        ];
        yield 'mixed' => [
            'expected' => [
                MethodSignatureGroup::create($methodSignatures[0], MethodsCollection::create($methods[0], $methods[2])),
                MethodSignatureGroup::create($methodSignatures[1], MethodsCollection::create($methods[1])),
                MethodSignatureGroup::create($methodSignatures[2], MethodsCollection::create($methods[3])),
            ],
            'methods' => $methods,
        ];
    }

    private function createMethod(MethodSignature $methodSignature): Method
    {
        return Method::create(
            $methodSignature,
            '',
            '',
            $this->createMock(CodePositionRange::class),
            '',
        );
    }
}
