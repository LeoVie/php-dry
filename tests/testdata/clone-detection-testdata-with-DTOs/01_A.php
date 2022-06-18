<?php /** @noinspection ALL */

class A
{
    /**
     * @return array<int, int>
     */
    function bar(DTO $dto): array
    {
        $r = [];
        $f = 100;
        foreach ($dto->getP1() as $i) {
            $r[] = $i * $dto->getP2();
        }
        return $r;
    }
}