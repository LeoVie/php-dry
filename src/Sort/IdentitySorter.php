<?php

declare(strict_types=1);

namespace App\Sort;

class IdentitySorter
{
    private const A_SAME_AS_B = 0;
    private const A_LOWER_THAN_B = -1;
    private const A_GREATER_THAN_B = 1;

    /**
     * @param Identity[] $identities
     *
     * @return Identity[]
     */
    public function sort(array $identities): array
    {
        usort($identities, fn(Identity $a, Identity $b): int => $this->compareIdentities($a, $b));

        return $identities;
    }

    private function compareIdentities(Identity $a, Identity $b): int
    {
        $aIdentity = $a->identity();
        $bIdentity = $b->identity();
        if ($aIdentity === $bIdentity) {
            return self::A_SAME_AS_B;
        }

        return ($aIdentity < $bIdentity) ? self::A_LOWER_THAN_B : self::A_GREATER_THAN_B;
    }
}