<?php /** @noinspection ALL */

class A_Changed_Statement_Order
{
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