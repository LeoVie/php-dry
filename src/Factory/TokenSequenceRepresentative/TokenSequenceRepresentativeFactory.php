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
        private MethodsCollectionFactory                    $methodsCollectionFactory,
    )
    {
    }

    /**
     * @param MethodsCollection[] $methodsCollections
     *
     * @return ExactTokenSequenceRepresentative[]
     * @throws CollectionCannotBeEmpty
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

    /**
     * @return ExactTokenSequenceRepresentative[]
     * @throws CollectionCannotBeEmpty
     */
    private function createMultipleForOneMethodsCollection(MethodsCollection $methodsCollection): array
    {
        $methodTokenSequences = array_map(fn(Method $m): MethodTokenSequence => MethodTokenSequence::create(
            $m,
            $this->tokenSequenceNormalizer->normalizeLevel1($this->tokenSequenceFactory->create('<?php ' . $m->getContent()))
        ), $methodsCollection->getAll());

        $groupedByTokenSequences = $this->methodTokenSequencesByTokenSequencesGrouper->group($methodTokenSequences);

        return $this->createMultipleForMultipleMethodTokenSequencesGroups($groupedByTokenSequences);
    }

    /**
     * @param array<MethodTokenSequence[]> $methodTokenSequenceGroups
     *
     * @return ExactTokenSequenceRepresentative[]
     * @throws CollectionCannotBeEmpty
     */
    private function createMultipleForMultipleMethodTokenSequencesGroups(array $methodTokenSequenceGroups): array
    {
        return array_map(fn(array $methodTokenSequences): ExactTokenSequenceRepresentative => ExactTokenSequenceRepresentative::create(
            $methodTokenSequences[0]->getTokenSequence(),
            $this->methodsCollectionFactory->fromHasMethods($methodTokenSequences),
        ), $methodTokenSequenceGroups);
    }
}