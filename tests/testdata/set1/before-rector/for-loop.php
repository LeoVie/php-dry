<?php

$array = [1, 2, 3, 4, 5];
$result = [];

for ($i = 0; $i < count($array); $i++) {
    $result[] = $array[$i] * 2;
}