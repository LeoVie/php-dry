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
        if (empty($identities)) {
            return [];
        }

        $result = [];
        $last = null;

        $sorted = $this->identitySorter->sort($identities);

        $group = $this->newGroup();

        foreach ($sorted as $i => $identity) {
            $s = $identity->identity();

            $isNotFirst = $i > 0;
            $notSameIdentityAsLast = $s !== $last;

            if ($isNotFirst && $notSameIdentityAsLast) {
                $result = $this->addGroupToResult($group, $result);
                $group = $this->newGroup();
            }

            $group[] = $identity;

            $last = $s;
        }

        return $this->addGroupToResult($group, $result);
    }

    private function addGroupToResult(array $group, array $result): array
    {
        $result[] = $group;

        return $result;
    }

    private function newGroup(): array
    {
        return [];
    }
}