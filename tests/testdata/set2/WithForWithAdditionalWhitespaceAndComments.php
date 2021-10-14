<?php /** @noinspection ALL */

class WithFor
{
    /**
     * @param array $array
     * @param int $factor
     * @return array
     */
    public function doSomething(array $array, int $factor): array
    {
        /* create the result array */
        $result = [];
        for (
            $i = 0;
            $i < count($array);
            $i++
        ) {
            // Here is the actual action!
            $result[] = $array[$i] * $factor;
        }

        // return the result
        return $result;
    }
}