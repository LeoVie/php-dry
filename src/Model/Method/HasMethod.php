<?php

declare(strict_types=1);

namespace App\Model\Method;

interface HasMethod
{
    public function getMethod(): Method;
}