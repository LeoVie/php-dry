<?php

namespace App\Tests\Unit\Output\Html;

use App\Output\Html\Attribute;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    /** @dataProvider asCodeProvider */
    public function testAsCode(string $expected, Attribute $attribute): void
    {
        self::assertSame($expected, $attribute->asCode());
    }

    public function asCodeProvider(): array
    {
        return [
            [
                'expected' => 'key="value"',
                'attribute' => Attribute::create('key', 'value'),
            ],
            [
                'expected' => 'type="button"',
                'attribute' => Attribute::create('type', 'button'),
            ],
        ];
    }
}