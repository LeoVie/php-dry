<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Finder\Finder;

class FinderService
{
    public function instance(): Finder
    {
        return Finder::create();
    }
}