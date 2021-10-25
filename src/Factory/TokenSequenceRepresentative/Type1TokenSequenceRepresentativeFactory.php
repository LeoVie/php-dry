<?php

declare(strict_types=1);

namespace App\Factory\TokenSequenceRepresentative;

use App\Collection\MethodsCollection;
use App\Exception\CollectionCannotBeEmpty;
use App\Factory\Collection\MethodsCollectionFactory;
use App\Factory\TokenSequenceFactory;
use App\Grouper\MethodTokenSequencesByTokenSequencesGrouper;
use App\Model\Method\Method;
use App\Model\Method\MethodTokenSequence;
use App\Model\TokenSequenceRepresentative\Type1TokenSequenceRepresentative;
use App\Util\ArrayUtil;
use LeoVie\PhpTokenNormalize\Service\TokenSequenceNormalizer;

class Type1TokenSequenceRepresentativeFactory
{
    public function __construct(
        private MethodTokenSequencesByTokenSequencesGrouper $methodTokenSequencesByTokenSequencesGrouper,
        private TokenSequenceFactory                        $tokenSequenceFactory,
        private TokenSequenceNormalizer                     $tokenSequenceNormalizer,
        private ArrayUtil                                   $arrayUtil,
        private MethodsCollectionFactory                    $methodsCollectionFactory,
    )
    {
    }

    /**
     * @param MethodsCollection[] $methodsCollections
     *
     * @return Type1TokenSequenceRepresentative[]
     * @throws CollectionCannotBeEmpty
     */
    public function createMultiple(array $methodsCollections): array
    {
        return $this->arrayUtil->flatten(
            array_map(
                fn(MethodsCollection $mc): array => $this->createMultipleForOneMethodsCollection($mc),
                $methodsCollections
            )
        );
    }

    /**
     * @return Type1TokenSequenceRepresentative[]
     * @throws CollectionCannotBeEmpty
     */
    private function createMultipleForOneMethodsCollection(MethodsCollection $methodsCollection): array
    {
        $methodTokenSequences = array_map(fn(Method $m): MethodTokenSequence => MethodTokenSequence::create(
            $m,
            $this->tokenSequenceNormalizer->normalizeLevel1($this->tokenSequenceFactory->createFromMethod($m))
        ), $methodsCollection->getAll());

        $groupedMethodTokenSequences = $this->methodTokenSequencesByTokenSequencesGrouper->group($methodTokenSequences);

        return $this->createMultipleForMultipleMethodTokenSequencesGroups($groupedMethodTokenSequences);
    }

    /**
     * @param array<MethodTokenSequence[]> $groupedMethodTokenSequences
     *
     * @return Type1TokenSequenceRepresentative[]
     * @throws CollectionCannotBeEmpty
     */
    private function createMultipleForMultipleMethodTokenSequencesGroups(array $groupedMethodTokenSequences): array
    {
        return array_map(fn(array $methodTokenSequences): Type1TokenSequenceRepresentative => Type1TokenSequenceRepresentative::create(
            $methodTokenSequences[0]->getTokenSequence(),
            $this->methodsCollectionFactory->fromMethodTokenSequence($methodTokenSequences),
        ), $groupedMethodTokenSequences);
    }
}