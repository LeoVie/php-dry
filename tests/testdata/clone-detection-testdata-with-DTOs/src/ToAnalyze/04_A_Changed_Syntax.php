<?php /** @noinspection ALL */

namespace LeoVie\CloneDetectionTestdataWithDTOs\ToAnalyze;

use LeoVie\DTORepository\VendorDTO;

class A_Changed_Syntax
{
    /**
     * @return array<int, int>
     */
    function foo(VendorDTO $object): array
    {
        return array_map(
            fn($i) => $i * $object->getP2(),
            $object->getP1()
        );
    }
}