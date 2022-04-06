<?php /** @noinspection ALL */

class B
{
    /**
     * @param array<int, int> $p1
     *
     * @return array<int, array<int, int>|int>
     */
    function foo(array $p1, int $p2): array
    {
        return [$p1, $p2];
    }
}