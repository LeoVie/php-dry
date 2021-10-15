<?php

declare(strict_types=1);

namespace App\Tests\Unit\Exception;

use App\Exception\NodeTypeNotConvertable;
use PHPUnit\Framework\TestCase;

class NodeTypeNotConvertableTest extends TestCase
{
    /** @dataProvider createProvider */
    public function testCreate(string $expectedMessage, string $nodeType): void
    {
        self::assertSame($expectedMessage, NodeTypeNotConvertable::create($nodeType)->getMessage());
    }

    public function createProvider(): array
    {
        return [
            [
                'Node type Foo is not convertable.',
                'Foo',
            ],
            [
                'Node type Bar is not convertable.',
                'Bar',
            ],
        ];
    }
}