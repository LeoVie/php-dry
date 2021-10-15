<?php

declare(strict_types=1);

namespace App\Factory\TokenSequenceRepresentative;

use App\Collection\MethodsCollection;
use App\Factory\TokenSequenceFactory;
use App\Grouper\MethodTokenSequencesByTokenSequencesGrouper;
use App\Model\Method\Method;
use App\Model\Method\MethodTokenSequence;
use App\Model\TokenSequenceRepresentative\TokenSequenceRepresentative;
use App\Tokenize\TokenSequenceNormalizer;

class TokenSequenceRepresentativeFactory
{
    public function __construct(
        private MethodTokenSequencesByTokenSequencesGrouper $methodTokenSequencesByTokenSequencesGrouper,
        private TokenSequenceFactory                        $tokenSequenceFactory,
        private TokenSequenceNormalizer                     $tokenSequenceNormalizer,
    )
    {
    }

    /**
     * @param MethodsCollection[] $methodsCollections
     *
     * @return TokenSequenceRepresentative[]
     */
    public function createMultipleByMethodsCollections(array $methodsCollections): array
    {
        return $this->arrayFlatten(array_map(function (MethodsCollection $mc): array {
            return $this->createTokenSequenceRepresentativesForMethodsCollection($mc);
        }, $methodsCollections));
    }

    /** @return TokenSequenceRepresentative[] */
    private function createTokenSequenceRepresentativesForMethodsCollection(MethodsCollection $methodsCollection): array
    {
        $methodTokenSequences = array_map(function (Method $m): MethodTokenSequence {
            return MethodTokenSequence::create($m,
                $this->tokenSequenceNormalizer->normalizeLevel1($this->tokenSequenceFactory->create('<?php ' . $m->getContent()))
            );
        }, $methodsCollection->getAll());

        $groupedByTokenSequences = $this->methodTokenSequencesByTokenSequencesGrouper->group($methodTokenSequences);

        return array_map(function (array $group): TokenSequenceRepresentative {
            $methodsCollection = MethodsCollection::empty();

            foreach ($group as $methodTokenSequence) {
                $methodsCollection->add($methodTokenSequence->getMethod());
            }

            return TokenSequenceRepresentative::create($group[0]->getTokenSequence(), $methodsCollection);
        }, $groupedByTokenSequences);
    }

    /**
     * @param array<array> $a
     *
     * @return array<mixed>
     */
    private function arrayFlatten(array $a): array
    {
        return array_merge(...$a);
    }
}