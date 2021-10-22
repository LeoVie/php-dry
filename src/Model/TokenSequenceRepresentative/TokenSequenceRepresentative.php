<?php

declare(strict_types=1);

namespace App\Model\TokenSequenceRepresentative;

use App\Collection\MethodsCollection;

interface TokenSequenceRepresentative
{
    public function getMethodsCollection(): MethodsCollection;
}