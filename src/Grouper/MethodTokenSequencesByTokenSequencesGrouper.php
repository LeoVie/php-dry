<?php

declare(strict_types=1);

namespace App\Grouper;

use App\Model\Method\MethodTokenSequence;
use App\Sort\IdentitySorter;

class MethodTokenSequencesByTokenSequencesGrouper
{
    public function __construct(private IdentitySorter $identitySorter)
    {
    }

    /**
     * @param MethodTokenSequence[] $methodTokenSequences
     *
     * @return array<MethodTokenSequence[]>
     */
    public function group(array $methodTokenSequences): array
    {
        $result = [];
        $last = null;

        $group = 0;
        foreach ($this->identitySorter->sort($methodTokenSequences) as $i => $methodTokenSequence) {
            $s = $methodTokenSequence->identity();
            if ($i > 0 && $s !== $last) {
                $group++;
            }

            $result[$group][] = $methodTokenSequence;

            $last = $s;
        }

        return $result;
    }
}