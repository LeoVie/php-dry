<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use Symfony\Component\Stopwatch\Stopwatch;

final class StopwatchFactory
{
    public static function create(): Stopwatch
    {
        return new Stopwatch();
    }
}