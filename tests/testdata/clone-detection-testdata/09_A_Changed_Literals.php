<?php /** @noinspection ALL */

class A_Changed_Literals
{
    function foo(array $p1, int $p2): array
    {
        $r = [];
        $f = -20;
        foreach ($p1 as $i) {
            $r[] = $i * $p2;
        }
        return $r;
    }
}