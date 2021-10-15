<?php

declare(strict_types=1);

namespace App\Model\TokenSequenceRepresentative;

use App\Collection\MethodsCollection;
use App\Tokenize\TokenSequence;

interface TokenSequenceRepresentative
{
    public static function create(TokenSequence $tokenSequence, MethodsCollection $methodsCollection): self;

    public function getTokenSequence(): TokenSequence;

    public function getMethodsCollection(): MethodsCollection;
}