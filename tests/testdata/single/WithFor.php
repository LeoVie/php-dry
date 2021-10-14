<?php /** @noinspection ALL */

class WithFor
{
    public function doSomething(array $array, int $factor): array
    {
        // this is a comment
        $result = [];
        for ($i = 0; $i < count($array); $i++) {
            $result[] = $array[$i] * $factor;
        }

        return $result;
    }
}