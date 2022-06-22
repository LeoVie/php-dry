<?php /** @noinspection ALL */

namespace LeoVie\CloneDetectionTestdataWithDTOs;

class A_Changed_Syntax
{
    /**
     * @param array<int, int> $p1
     *
     * @return array<int, int>
     */
    function foo(array $p1, int $p2): array
    {
        return array_map(
            fn($i) => $i * $p2,
            $p1
        );
    }
}