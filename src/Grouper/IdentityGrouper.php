<?php

declare(strict_types=1);

namespace App\Grouper;

use App\Sort\Identity;
use App\Sort\IdentitySorter;

class IdentityGrouper
{
    public function __construct(private IdentitySorter $identitySorter)
    {
    }

    /**
     * @param Identity[] $identities
     *
     * @return array<Identity[]>
     */
    public function group(array $identities): array
    {
        $result = [];
        $last = null;

        $group = 0;
        $sorted = $this->identitySorter->sort($identities);
        foreach ($sorted as $i => $methodTokenSequence) {
            $s = $methodTokenSequence->identity();
            if ($i > 0 && $s !== $last) {
                $group++;
            }

            $result[$group][] = $methodTokenSequence;

            $last = $s;
        }

        return $result;
    }
}