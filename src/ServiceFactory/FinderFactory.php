<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use Symfony\Component\Finder\Finder;

final class FinderFactory
{
    public function instance(): Finder
    {
        return Finder::create();
    }
}