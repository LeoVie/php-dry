<?php /** @noinspection ALL */

namespace LeoVie\CloneDetectionTestdataWithNativeTypes;

class A_Changed_Param_Order
{
    /**
     * @param array<int, int> $p1
     *
     * @return array<int, int>
     */
    function foo(int $p2, array $p1): array
    {
        $r = [];
        $f = 100;
        foreach ($p1 as $i) {
            $r[] = $i * $p2;
        }
        return $r;
    }
}