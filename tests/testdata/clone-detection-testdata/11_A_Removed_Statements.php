<?php /** @noinspection ALL */

class A_Removed_Statements
{
    function foo(array $p1, int $p2): array
    {
        $r = [];
        foreach ($p1 as $i) {
            $r[] = $i * $p2;
        }
        return $r;
    }
}