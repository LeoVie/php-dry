<?php /** @noinspection ALL */

class WithArrayMap
{
    public function multiplyBy(array $subject, int $by): array
    {
        return array_map(fn(int $x) => $x * $by, $subject);
    }
}