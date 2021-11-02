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
        private ArrayUtil                                   $arrayUtil,
        private MethodsCollectionFactory                    $methodsCollectionFactory,
    )
    {
    }

    /**
     * @param MethodSignatureGroup[] $methodSignatureGroups
     *
     * @return Type1SourceCloneCandidate[]
     * @throws CollectionCannotBeEmpty
     */
    public function createMultiple(array $methodSignatureGroups): array
    {
        return $this->arrayUtil->flatten(
            array_map(
                fn(MethodSignatureGroup $msg): array => $this->createMultipleForOneMethodsCollection($msg),
                $methodSignatureGroups
            )
        );
    }

    /**
     * @return Type1SourceCloneCandidate[]
     * @throws CollectionCannotBeEmpty
     */
    private function createMultipleForOneMethodsCollection(MethodSignatureGroup $methodSignatureGroup): array
    {
        $methodTokenSequences = array_map(fn(Method $m): MethodTokenSequence => MethodTokenSequence::create(
            $m,
            $this->tokenSequenceNormalizer->normalizeLevel1($this->tokenSequenceFactory->createFromMethod($m))
        ), $methodSignatureGroup->getMethodsCollection()->getAll());

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
        return array_map(fn(array $methodTokenSequences): Type1SourceCloneCandidate => Type1SourceCloneCandidate::create(
            $methodTokenSequences[0]->getTokenSequence(),
            $this->methodsCollectionFactory->fromMethodTokenSequence($methodTokenSequences),
        ), $groupedMethodTokenSequences);
    }
}