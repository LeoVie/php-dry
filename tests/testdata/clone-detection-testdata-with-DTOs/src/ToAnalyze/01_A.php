<?php /** @noinspection ALL */

namespace LeoVie\CloneDetectionTestdataWithDTOs\ToAnalyze;

use LeoVie\DTORepository\VendorDTO;

class A
{
    /**
     * @return array<int, int>
     */
    function bar(VendorDTO $dto): array
    {
        $r = [];
        $f = 100;
        foreach ($dto->getP1() as $i) {
            $r[] = $i * $dto->getP2();
        }
        return $r;
    }
}