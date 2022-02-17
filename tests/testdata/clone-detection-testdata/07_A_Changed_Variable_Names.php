<?php /** @noinspection ALL */

class A_Changed_Variable_Names
{
    function foo(array $first, int $second): array
    {
        $x = [];
        $k = 100;
        foreach ($first as $y) {
            $x[] = $y * $second;
        }
        return $x;
    }
}