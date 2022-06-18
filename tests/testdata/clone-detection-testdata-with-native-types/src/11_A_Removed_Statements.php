<?php /** @noinspection ALL */

namespace LeoVie\CloneDetectionTestdataWithNativeTypes;

class A_Removed_Statements
{
    /**
     * @param array<int, int> $p1
     *
     * @return array<int, int>
     */
    function foo(array $p1, int $p2): array
    {
        $r = [];
        foreach ($p1 as $i) {
            $r[] = $i * $p2;
        }
        return $r;
    }
}