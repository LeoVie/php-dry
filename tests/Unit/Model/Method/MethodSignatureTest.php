<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Method;

use App\Model\Method\MethodSignature;
use Generator;
use PHPUnit\Framework\TestCase;

class MethodSignatureTest extends TestCase
{
    /** @dataProvider getParamTypesProvider */
    public function testGetParamTypes(array $expected, MethodSignature $methodSignature): void
    {
        self::assertSame($expected, $methodSignature->getParamTypes());
    }

    public function getParamTypesProvider(): Generator
    {
        $paramTypes = [];
        yield [$paramTypes, MethodSignature::create($paramTypes, [], '')];

        $paramTypes = ['string'];
        yield [$paramTypes, MethodSignature::create($paramTypes, [0], '')];

        $paramTypes = ['int', 'string'];
        yield [$paramTypes, MethodSignature::create($paramTypes, [0, 1], '')];
    }

    /** @dataProvider getReturnTypeProvider */
    public function testGetReturnType(string $expected, MethodSignature $methodSignature): void
    {
        self::assertSame($expected, $methodSignature->getReturnType());
    }

    public function getReturnTypeProvider(): Generator
    {
        $returnType = 'int';
        yield [$returnType, MethodSignature::create([], [], $returnType)];

        $returnType = 'string';
        yield [$returnType, MethodSignature::create([], [], $returnType)];

        $returnType = '?int';
        yield [$returnType, MethodSignature::create([], [], $returnType)];
    }

    /** @dataProvider jsonSerializeProvider */
    public function testJsonSerialize(string $expected, MethodSignature $methodSignature): void
    {
        self::assertJsonStringEqualsJsonString($expected, \Safe\json_encode($methodSignature));
    }

    public function jsonSerializeProvider(): Generator
    {
        yield [
            'expected' => \Safe\json_encode([
                'paramTypes' => [],
                'returnType' => 'int',
            ]),
            MethodSignature::create([], [], 'int')
        ];

        yield [
            'expected' => \Safe\json_encode([
                'paramTypes' => [],
                'returnType' => '?array',
            ]),
            MethodSignature::create([], [], '?array')
        ];

        yield [
            'expected' => \Safe\json_encode([
                'paramTypes' => ['int'],
                'returnType' => 'int',
            ]),
            MethodSignature::create(['int'], [0], 'int')
        ];

        yield [
            'expected' => \Safe\json_encode([
                'paramTypes' => ['int', 'string'],
                'returnType' => 'int',
            ]),
            MethodSignature::create(['int', 'string'], [0, 1], 'int')
        ];
    }
}
