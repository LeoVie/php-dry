<?php /** @noinspection ALL */

class A_Additional_Statements
{
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