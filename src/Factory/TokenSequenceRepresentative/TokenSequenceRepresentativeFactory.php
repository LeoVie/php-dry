<?php

declare(strict_types=1);

namespace App\Factory\TokenSequenceRepresentative;

use App\Collection\MethodsCollection;
use App\Factory\TokenSequenceFactory;
use App\Grouper\MethodTokenSequencesByTokenSequencesGrouper;
use App\Model\Method\Method;
use App\Model\Method\MethodTokenSequence;
use App\Model\TokenSequenceRepresentative\ExactTokenSequenceRepresentative;
use App\Tokenize\TokenSequenceNormalizer;
use App\Util\ArrayUtil;

class TokenSequenceRepresentativeFactory
{
    public function __construct(
        private MethodTokenSequencesByTokenSequencesGrouper $methodTokenSequencesByTokenSequencesGrouper,
        private TokenSequenceFactory                        $tokenSequenceFactory,
        private TokenSequenceNormalizer                     $tokenSequenceNormalizer,
        private ArrayUtil                                   $arrayUtil,
    )
    {
    }

    /**
     * @param MethodsCollection[] $methodsCollections
     *
     * @return ExactTokenSequenceRepresentative[]
     */
    public function createMultipleForMultipleMethodsCollections(array $methodsCollections): array
    {
        return $this->arrayUtil->flatten(
            array_map(
                fn(MethodsCollection $mc): array => $this->createMultipleForOneMethodsCollection($mc),
                $methodsCollections
            )
        );
    }

    /** @return ExactTokenSequenceRepresentative[] */
    private function createMultipleForOneMethodsCollection(MethodsCollection $methodsCollection): array
    {
        $methodTokenSequences = array_map(function (Method $m): MethodTokenSequence {
            return MethodTokenSequence::create(
                $m,
                $this->tokenSequenceNormalizer->normalizeLevel1($this->tokenSequenceFactory->create('<?php ' . $m->getContent()))
            );
        }, $methodsCollection->getAll());

        $groupedByTokenSequences = $this->methodTokenSequencesByTokenSequencesGrouper->group($methodTokenSequences);

        return $this->createMultipleForMultipleMethodTokenSequencesGroups($groupedByTokenSequences);
    }

    /**
     * @param array<MethodTokenSequence[]> $methodTokenSequenceGroups
     *
     * @return ExactTokenSequenceRepresentative[]
     */
    private function createMultipleForMultipleMethodTokenSequencesGroups(array $methodTokenSequenceGroups): array
    {
        return array_map(function (array $methodTokenSequences): ExactTokenSequenceRepresentative {
            return ExactTokenSequenceRepresentative::create(
                $methodTokenSequences[0]->getTokenSequence(),
                $this->createMethodsCollectionForMethodTokenSequences($methodTokenSequences)
            );
        }, $methodTokenSequenceGroups);
    }

    /** @param MethodTokenSequence[] $methodTokenSequences */
    private function createMethodsCollectionForMethodTokenSequences(array $methodTokenSequences): MethodsCollection
    {
        return MethodsCollection::withInitialContent(
            ...array_map(
                fn(MethodTokenSequence $mts): Method => $mts->getMethod(),
                $methodTokenSequences
            )
        );
    }
}