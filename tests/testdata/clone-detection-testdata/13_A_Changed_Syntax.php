<?php /** @noinspection ALL */

class A_Changed_Syntax
{
    function foo(array $p1, int $p2): array
    {
        return array_map(
            fn(int $i) => $i * $p2,
            $p1
        );
    }
}
