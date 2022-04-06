<?php

declare(strict_types=1);

function multiplyBy(array $subject, int $by): array
{
    return array_map(fn(int $x) => $x * $by, $subject);
}

function doSomething(array $array, int $factor): array
{
    $result = [];
    for ($i = 0; $i < count($array); $i++) {
        $result[] = $array[$i] * $factor;
    }

    return $result;
}

function factorArray(array $array, int $factor): array
{
    $result = [];
    foreach ($array as $x) {
        $result[] = $x * $factor;
    }

    return $result;
}

function different(string $text): int
{
    return strlen($text) * 2;
}
