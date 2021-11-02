<?php

declare(strict_types=1);

namespace App\Model\SourceCloneCandidate;

use App\Collection\MethodsCollection;

interface SourceCloneCandidate
{
    public function getMethodsCollection(): MethodsCollection;
}