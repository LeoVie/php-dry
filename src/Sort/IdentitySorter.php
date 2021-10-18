<?php

declare(strict_types=1);

namespace App\Sort;

class IdentitySorter
{
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
            return 0;
        }

        return ($aIdentity < $bIdentity) ? -1 : 1;
    }
}