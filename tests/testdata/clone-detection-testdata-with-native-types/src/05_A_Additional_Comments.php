<?php /** @noinspection ALL */

namespace LeoVie\CloneDetectionTestdataWithNativeTypes;

class A_Additional_Comments
{
    /**
     * @param array<int, int> $p1
     *
     * @return array<int, int>
     */
    function foo(array $p1, int $p2): array
    {
        /*
         * Some actions are following:
         */
        // first we do this
        $r = [];
        $f = 100;
        // then in the loop
        foreach ($p1 as $i) {
            // we do this
            $r[] = $i * $p2;
        }
        // and after that, we do this
        return $r;
    }
}