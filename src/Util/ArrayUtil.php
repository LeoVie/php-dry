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
        if (count($array) <= 1) {
            return array_values($array);
        }

        $result = [];

        foreach ($array as $i => $a) {
            foreach ($array as $j => $b) {
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
}