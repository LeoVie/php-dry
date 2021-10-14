<?php

declare(strict_types=1);

namespace App\Tests\Unit\Exception;

use App\Exception\OtherNodeTypeExpected;
use PHPUnit\Framework\TestCase;

class OtherNodeTypeExpectedTest extends TestCase
{
    /** @dataProvider createProvider */
    public function testCreate(string $expectedMessage, string $expectedNodeType, string $actualNodeType): void
    {
        self::assertSame($expectedMessage, OtherNodeTypeExpected::create($expectedNodeType, $actualNodeType)->getMessage());
    }

    public function createProvider(): array
    {
        return [
            [
                'expectedMessage' => 'Expected node type Foo, but actual got Bar.',
                'expectedNodeType' => 'Foo',
                'actualNodeType' => 'Bar',
            ],
            [
                'expectedMessage' => 'Expected node type BlaBla, but actual got FooFoo.',
                'expectedNodeType' => 'BlaBla',
                'actualNodeType' => 'FooFoo',
            ],
        ];
    }
}