<?php

declare(strict_types=1);

namespace App\Factory\SourceCloneCandidate;

use App\Exception\CollectionCannotBeEmpty;
use App\Factory\Collection\MethodsCollectionFactory;
use App\Factory\TokenSequenceFactory;
use App\Grouper\MethodTokenSequencesByTokenSequencesGrouper;
use App\Model\Method\Method;
use App\Model\Method\MethodSignatureGroup;
use App\Model\Method\MethodTokenSequence;
use App\Model\SourceCloneCandidate\Type1SourceCloneCandidate;
use App\Util\ArrayUtil;
use LeoVie\PhpTokenNormalize\Service\TokenSequenceNormalizer;

class Type1SourceCloneCandidateFactory
{
    public function __construct(
        private MethodTokenSequencesByTokenSequencesGrouper $methodTokenSequencesByTokenSequencesGrouper,
        private TokenSequenceFactory                        $tokenSequenceFactory,
        private TokenSequenceNormalizer                     $tokenSequenceNormalizer,
        private MethodsCollectionFactory                    $methodsCollectionFactory,
        private ArrayUtil                                   $arrayUtil,
    )
    {
    }

    /**
     * @param iterable<MethodSignatureGroup> $methodSignatureGroups
     *
     * @return Type1SourceCloneCandidate[]
     * @throws CollectionCannotBeEmpty
     */
    public function createMultiple(iterable $methodSignatureGroups): array
    {
        $sourceCloneCandidatesNested = [];
        foreach ($methodSignatureGroups as $msg) {
            $sourceCloneCandidatesNested[] = $this->createMultipleForOneMethodsCollection($msg);
        }

        /** @var Type1SourceCloneCandidate[] $sourceClonesCandidates */
        $sourceClonesCandidates = $this->arrayUtil->flatten($sourceCloneCandidatesNested);

        return $sourceClonesCandidates;
    }

    /**
     * @return Type1SourceCloneCandidate[]
     * @throws CollectionCannotBeEmpty
     */
    private function createMultipleForOneMethodsCollection(MethodSignatureGroup $methodSignatureGroup): array
    {
        $methodTokenSequences = [];
        foreach ($methodSignatureGroup->getMethodsCollection()->getAll() as $method) {
            $methodTokenSequences[] = $this->createMethodTokenSequence($method);
        }

        $groupedMethodTokenSequences = $this->methodTokenSequencesByTokenSequencesGrouper->group($methodTokenSequences);

        return $this->createMultipleForMultipleMethodTokenSequencesGroups($groupedMethodTokenSequences);
    }

    /**
     * @param array<MethodTokenSequence[]> $groupedMethodTokenSequences
     *
     * @return Type1SourceCloneCandidate[]
     * @throws CollectionCannotBeEmpty
     */
    private function createMultipleForMultipleMethodTokenSequencesGroups(array $groupedMethodTokenSequences): array
    {
        $type1SourceCloneCandidates = [];
        foreach ($groupedMethodTokenSequences as $methodTokenSequences) {
            $type1SourceCloneCandidates[] = Type1SourceCloneCandidate::create(
                $methodTokenSequences[0]->getTokenSequence(),
                $this->methodsCollectionFactory->fromMethodTokenSequence($methodTokenSequences),
            );
        }

        return $type1SourceCloneCandidates;
    }

    private function createMethodTokenSequence(Method $method): MethodTokenSequence
    {
        $normalizedTokenSequence = $this->tokenSequenceNormalizer->normalizeLevel1($this->tokenSequenceFactory->createFromMethod($method));

        return MethodTokenSequence::create(
            $method,
            $normalizedTokenSequence,
            $normalizedTokenSequence->identity()
        );
    }
}
