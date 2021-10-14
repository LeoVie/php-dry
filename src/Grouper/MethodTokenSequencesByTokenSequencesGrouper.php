<?php

declare(strict_types=1);

namespace App\Grouper;

use App\Model\Method\MethodTokenSequence;

class MethodTokenSequencesByTokenSequencesGrouper
{

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
        foreach ($this->sort($methodTokenSequences) as $i => $methodTokenSequence) {
            $s = $methodTokenSequence->identity();
            if ($i > 0 && $s !== $last) {
                $group++;
            }

            $result[$group][] = $methodTokenSequence;

            $last = $s;
        }

        return $result;
    }

    /**
     * @param MethodTokenSequence[] $methodTokenSequences
     *
     * @return MethodTokenSequence[]
     */
    private function sort(array $methodTokenSequences): array
    {
        usort($methodTokenSequences, function (MethodTokenSequence $a, MethodTokenSequence $b): int {
            $aIdentity = $a->identity();
            $bIdentity = $b->identity();
            if ($aIdentity === $bIdentity) {
                return 0;
            }

            return ($aIdentity < $bIdentity) ? -1 : 1;
        });

        return $methodTokenSequences;
    }
}