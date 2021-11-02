<?php /** @noinspection ALL */

class Type4DifferentImplementation
{
    public function otherName(array $items, int $n): array
    {
        return array_map(fn(int $x): int => $x * $n, $items);
    }
}