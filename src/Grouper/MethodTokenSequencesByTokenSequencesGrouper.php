<?php

declare(strict_types=1);

namespace App\Grouper;

use App\Model\Method\MethodTokenSequence;
use LeoVie\PhpGrouper\Service\Grouper;

class MethodTokenSequencesByTokenSequencesGrouper
{
    public function __construct(private Grouper $grouper)
    {
    }

    /**
     * @param MethodTokenSequence[] $methodTokenSequences
     *
     * @return array<MethodTokenSequence[]>
     */
    public function group(array $methodTokenSequences): array
    {
        /** @var array<MethodTokenSequence[]> $grouped */
        $grouped = $this->grouper->groupByGroupID($methodTokenSequences);

        return $grouped;
    }
}
