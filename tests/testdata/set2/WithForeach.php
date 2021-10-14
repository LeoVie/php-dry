<?php /** @noinspection ALL */

class WithForeach
{
    public function factorArray(array $array, int $factor): array
    {
        $result = [];
        foreach ($array as $x) {
            $result[] = $x * $factor;
        }

        return $result;
    }
}