<?php

declare(strict_types=1);

namespace App\Grouper;

use App\Model\Method\MethodTokenSequence;

class MethodTokenSequencesByTokenSequencesGrouper
{
    public function __construct(private StringableByValueGrouper $stringableByValueGrouper)
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
        $grouped = $this->stringableByValueGrouper->group($methodTokenSequences);

        return $grouped;
    }
}