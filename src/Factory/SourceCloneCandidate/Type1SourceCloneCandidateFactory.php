<?php

declare(strict_types=1);

namespace App\Factory\SourceCloneCandidate;

use App\Cache\MethodTokenSequenceCache;
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
        private MethodTokenSequenceCache                    $methodTokenSequenceCache,
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
            $methodTokenSequences[] = $this->getMethodTokenSequenceFromCacheOrCreate($method);
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

    private function getMethodTokenSequenceFromCacheOrCreate(Method $method): MethodTokenSequence
    {
        $methodTokenSequence = $this->methodTokenSequenceCache->get($method);
        if ($methodTokenSequence === null) {
            $methodTokenSequence = MethodTokenSequence::create(
                $method,
                $this->tokenSequenceNormalizer->normalizeLevel1($this->tokenSequenceFactory->createFromMethod($method))
            );

            $this->methodTokenSequenceCache->store($method, $methodTokenSequence);
        }

        return $methodTokenSequence;
    }
}
