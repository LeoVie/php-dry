<?php

declare(strict_types=1);

namespace App\Util;

class ArrayUtil
{
    /**
     * @param array<array> $a
     *
     * @return array<mixed>
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
}