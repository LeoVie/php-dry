<?php

declare(strict_types=1);

namespace App\Sort;

interface Identity
{
    public function identity(): string;
}