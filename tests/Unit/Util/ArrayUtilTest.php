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

    /** @dataProvider arrayContainsOtherArrayProvider */
    public function testArrayContainsOtherArray(bool $expected, array $a, array $b): void
    {
        self::assertSame($expected, (new ArrayUtil())->arrayContainsOtherArray($a, $b));
    }

    public function arrayContainsOtherArrayProvider(): array
    {
        return [
            'first empty, second not' => [
                'expected' => false,
                'a' => [],
                'b' => [1, 2, 3],
            ],
            'second empty' => [
                'expected' => true,
                'a' => [1, 2, 3],
                'b' => [],
            ],
            'both empty' => [
                'expected' => true,
                'a' => [],
                'b' => [],
            ],
            'first equals second' => [
                'expected' => true,
                'a' => [1, 2, 3, 4],
                'b' => [1, 2, 3, 4],
            ],
            'first contains second' => [
                'expected' => true,
                'a' => [1, 2, 3, 4],
                'b' => [2, 3],
            ],
            'first contains second not' => [
                'expected' => false,
                'a' => [1, 2, 3, 4],
                'b' => [2, 3, 5],
            ],
        ];
    }

    /** @dataProvider removeSubsetsProvider */
    public function testRemoveSubsets(array $expected, array $array): void
    {
        self::assertSame($expected, (new ArrayUtil())->removeEntriesThatAreSubsetsOfOtherEntries($array));
    }

    public function removeSubsetsProvider(): array
    {
        return [
            'empty' => [
                'expected' => [],
                'array' => [],
            ],
            'only one entry' => [
                'expected' => [
                    [1, 2, 3]
                ],
                'array' => [
                    'key that should get removed' => [1, 2, 3]
                ],
            ],
            'no subsets contained' => [
                'expected' => [
                    [1, 2, 3],
                    [2, 3, 4],
                    [4, 5, 6],
                ],
                'array' => [
                    [1, 2, 3],
                    [2, 3, 4],
                    [4, 5, 6],
                ],
            ],
            'subsets contained' => [
                'expected' => [
                    [1, 2, 3],
                    [4, 5, 6],
                ],
                'array' => [
                    [1, 2, 3],
                    [2, 3],
                    [4, 5, 6],
                ],
            ],
        ];
    }
}