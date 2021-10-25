<?php

declare(strict_types=1);

namespace App\Service;

use App\Configuration\Configuration;
use App\Factory\TokenSequenceFactory;
use App\Model\Method\Method;
use App\Model\SourceClone\SourceClone;
use App\Util\ArrayUtil;

class IgnoreClonesService
{
    public function __construct(
        private ArrayUtil            $arrayUtil,
        private TokenSequenceFactory $tokenSequenceFactory,
    )
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
        return array_values(
            array_filter(
                $this->arrayUtil->flatten($cloneGroups),
                fn(SourceClone $c): bool => !$this->cloneShouldBeIgnored($c, $configuration)
            )
        );
    }

    private function cloneShouldBeIgnored(SourceClone $clone, Configuration $configuration): bool
    {
        $tokenLengths = array_map(function (Method $m): int {
            return $this->tokenSequenceFactory->create('<?php ' . $m->getContent())->length();
        }, $clone->getMethodsCollection()->getAll());

        return max($tokenLengths) < $configuration->minSimilarTokens();
    }
}