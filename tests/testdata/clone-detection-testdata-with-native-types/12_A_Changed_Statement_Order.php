<?php /** @noinspection ALL */

class A_Changed_Statement_Order
{
    /**
     * @param array<int, int> $p1
     *
     * @return array<int, int>
     */
    function foo(array $p1, int $p2): array
    {
        $f = 100;
        $r = [];
        foreach ($p1 as $i) {
            $r[] = $i * $p2;
        }
        return $r;
    }
}