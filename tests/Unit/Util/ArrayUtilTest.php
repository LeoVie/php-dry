<?php

declare(strict_types=1);

namespace App\Tests\Unit\Util;

use App\Util\ArrayUtil;
use PHPUnit\Framework\TestCase;

class ArrayUtilTest extends TestCase
{
    /** @dataProvider flattenProvider */
    public function testFlatten(array $expected, array $input, int $level): void
    {
        self::assertSame($expected, (new ArrayUtil())->flatten($input, $level));
    }

    public function flattenProvider(): array
    {
        return [
            'empty (level 1)' => [
                'expected' => [],
                'input' => [],
                'level' => 1,
            ],
            'empty (level 2)' => [
                'expected' => [],
                'input' => [],
                'level' => 2,
            ],
            'two arrays (level 1)' => [
                'expected' => [1, 2, 3, 4, 5, 6],
                'input' => [[1, 2, 3], [4, 5, 6]],
                'level' => 1,
            ],
            'two arrays (level 2)' => [
                'expected' => [1, 2, 3, 4, 5, 6],
                'input' => [[1, 2, 3], [4, 5, 6]],
                'level' => 2,
            ],
            'nested array (level 1)' => [
                'expected' => [1, 2, 3, 4, [5, 6]],
                'input' => [1, 2, [3, 4, [5, 6]]],
                'level' => 1,
            ],
            'nested array (level 2)' => [
                'expected' => [1, 2, 3, 4, 5, 6],
                'input' => [1, 2, [3, 4, [5, 6]]],
                'level' => 2,
            ],
        ];
    }
}