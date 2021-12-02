<?php

declare(strict_types=1);

namespace App\Util;

class ArrayUtil
{
    /**
     * @param mixed[] $a
     *
     * @return mixed[]
     */
    public function flatten(array $a, int $level = 1): array
    {
        if ($level === 0) {
            return $a;
        }

        $result = [];
        foreach ($a as $value) {
            if (is_array($value)) {
                $result = array_merge($result, $this->flatten($value, $level - 1));
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * @param array<mixed> $a
     * @param array<mixed> $b
     */
    public function arrayContainsOtherArray(array $a, array $b): bool
    {
        return count(array_intersect($a, $b)) === count($b);
    }

    /**
     * @param array<array<mixed>> $array
     *
     * @return array<array<mixed>>
     */
    public function removeEntriesThatAreSubsetsOfOtherEntries(array $array): array
    {
        /** @var array<array<mixed>> $uniqueArray */
        $uniqueArray = $this->unique($array);

        if (count($uniqueArray) <= 1) {
            return array_values($uniqueArray);
        }

        $result = [];

        foreach ($uniqueArray as $i => $a) {
            foreach ($uniqueArray as $j => $b) {
                if ($i === $j) {
                    continue;
                }

                if ($this->arrayContainsOtherArray($b, $a)) {
                    break;
                }

                if (in_array($a, $result)) {
                    break;
                }

                $result[] = $a;
            }
        }

        return $result;
    }

    /**
     * @param mixed[] $array
     * @return mixed[]
     */
    public function unique(array $array): array
    {
        $array = $this->nestedSort($array);

        $unique = [];
        foreach ($array as $item) {
            if (!in_array($item, $unique)) {
                $unique[] = $item;
            }
        }

        return $unique;
    }

    /**
     * @param mixed[] $array
     * @return mixed[]
     */
    private function nestedSort(array $array): array
    {
        $sorted = [];
        foreach ($array as $item) {
            if (!is_array($item)) {
                $sorted[] = $item;
            } else {
                $sorted[] = $this->nestedSort($item);
            }
        }

        sort($sorted);

        return $sorted;
    }
}