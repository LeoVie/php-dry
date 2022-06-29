<?php /** @noinspection ALL */

class A_Changed_Variable_Names
{
    /**
     * @param array<int, int> $first
     *
     * @return array<int, int>
     */
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