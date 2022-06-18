<?php /** @noinspection ALL */

namespace LeoVie\CloneDetectionTestdataWithNativeTypes;

class A_Additional_Statements
{
    /**
     * @param array<int, int> $p1
     *
     * @return array<int, int>
     */
    function foo(array $p1, int $p2): array
    {
        $r = [];
        $f = 100;
        foreach ($p1 as $i) {
            print($i * $p2 . "\n");
            $r[] = $i * $p2;
        }
        return $r;
    }
}