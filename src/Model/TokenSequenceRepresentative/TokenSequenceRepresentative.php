<?php

declare(strict_types=1);

namespace App\Model\TokenSequenceRepresentative;

use App\Collection\MethodsCollection;
use App\Tokenize\TokenSequence;

interface TokenSequenceRepresentative
{
    public function getMethodsCollection(): MethodsCollection;
}