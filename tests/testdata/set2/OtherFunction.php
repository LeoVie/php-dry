<?php /** @noinspection ALL */

class OtherFunction
{
    public function doSomethingOther(array $subject, int $by): array
    {
        return array_map(fn(int $x) => $x * $by + 100, $subject);
    }
}