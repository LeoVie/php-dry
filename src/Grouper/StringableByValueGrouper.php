<?php

declare(strict_types=1);

namespace App\Grouper;

use Stringable;

// TODO: sort by identity, not string
class StringableByValueGrouper
{
    /**
     * @param Stringable[] $stringables
     *
     * @return Stringable[][]
     */
    public function group(array $stringables): array
    {
        $result = [];
        $last = null;

        $group = 0;
        foreach ($this->sortByToString($stringables) as $i => $stringable) {
            $s = $stringable->__toString();
            if ($i > 0 && $s !== $last) {
                $group++;
            }

            $result[$group][] = $stringable;

            $last = $s;
        }

        return $result;
    }

    /**
     * @param Stringable[] $stringable
     *
     * @return Stringable[]
     */
    private function sortByToString(array $stringable): array
    {
        usort($stringable, function (Stringable $a, Stringable $b): int {
            $aString = $a->__toString();
            $bString = $b->__toString();
            if ($aString === $bString) {
                return 0;
            }

            return ($aString < $bString) ? -1 : 1;
        });

        return $stringable;
    }
}