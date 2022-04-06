<?php

declare(strict_types=1);

namespace App\Tests\Unit\Compare;

use App\Compare\MethodSignatureComparer;
use PHPUnit\Framework\TestCase;

class MethodSignatureComparerTest extends TestCase
{
    /** @dataProvider areEqualProvider */
    public function testAreEqual(\App\Model\Method\MethodSignature $a, \App\Model\Method\MethodSignature $b, bool $expected): void
    {
        self::assertSame($expected, (new MethodSignatureComparer())->areEqual($a, $b));
    }

    public function areEqualProvider(): array
    {
        return [
            'non equal param types -> not equal' => [
                \App\Model\Method\MethodSignature::create(['int'], 'string'),
                \App\Model\Method\MethodSignature::create(['string'], 'string'),
                'expected' => false,
            ],
            'non equal return type -> not equal' => [
                \App\Model\Method\MethodSignature::create(['int'], 'string'),
                \App\Model\Method\MethodSignature::create(['int'], 'int'),
                'expected' => false,
            ],
            'everything equal -> equal' => [
                \App\Model\Method\MethodSignature::create(['int'], 'string'),
                \App\Model\Method\MethodSignature::create(['int'], 'string'),
                'expected' => true,
            ],
        ];
    }
}
