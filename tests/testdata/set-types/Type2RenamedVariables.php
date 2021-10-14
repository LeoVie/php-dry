<?php /** @noinspection ALL */

class Type2RenamedVariables
{
    public function doSomething(array $items, int $n): array
    {
        $returnedValues = [];
        for ($i = 0; $i < count($items); $i++) {
            $returnedValues[] = $items[$i] * $n;
        }

        return $returnedValues;
    }
}