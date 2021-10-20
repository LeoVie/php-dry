<?php

declare(strict_types=1);

namespace App\Service;

use App\Configuration\Configuration;
use App\Model\Method\Method;
use App\Model\SourceClone\SourceClone;
use App\Util\ArrayUtil;

class IgnoreClonesService
{
    public function __construct(private ArrayUtil $arrayUtil)
    {
    }

    /**
     * @param SourceClone[][] $cloneGroups
     * @param Configuration $configuration
     *
     * @return SourceClone[]
     */
    public function extractNonIgnoredClones(array $cloneGroups, Configuration $configuration): array
    {
        return array_filter(
            $this->arrayUtil->flatten($cloneGroups),
            fn(SourceClone $c): bool => !$this->cloneShouldBeIgnored($c, $configuration)
        );
    }

    private function cloneShouldBeIgnored(SourceClone $clone, Configuration $configuration): bool
    {
        $methodLines = array_map(fn(Method $m): int => $m->getCodePositionRange()->countOfLines(), $clone->getMethodsCollection()->getAll());

        return max($methodLines) < $configuration->minLinesForType1AndType2Clones();
    }
}