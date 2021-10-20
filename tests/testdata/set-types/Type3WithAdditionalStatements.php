<?php /** @noinspection ALL */

class Type3WithAdditionalStatements
{
    public function doSomething(array $items, int $n): array
    {
        $returnedValues = [];
        print("$n");
        for ($i = 0; $i < count($items); $i++) {
            $returnedValues[] = $items[$i] * $n;
        }

        var_dump($items);

        return $returnedValues;
    }
}