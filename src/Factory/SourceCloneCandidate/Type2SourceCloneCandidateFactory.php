<?php

declare(strict_types=1);

namespace App\Factory\SourceCloneCandidate;

use App\Exception\CollectionCannotBeEmpty;
use App\Merge\Type2SourceCloneCandidatesMerger;
use App\Model\SourceCloneCandidate\Type1SourceCloneCandidate;
use App\Model\SourceCloneCandidate\Type2SourceCloneCandidate;
use LeoVie\PhpTokenNormalize\Service\TokenSequenceNormalizer;

class Type2SourceCloneCandidateFactory
{
    public function __construct(
        private TokenSequenceNormalizer          $tokenSequenceNormalizer,
        private Type2SourceCloneCandidatesMerger $type2SourceCloneCandidatesMerger,
    )
    {
    }

    /**
     * @param Type1SourceCloneCandidate[] $type1SourceCloneCandidates
     *
     * @return Type2SourceCloneCandidate[]
     * @throws CollectionCannotBeEmpty
     */
    public function createMultiple(iterable $type1SourceCloneCandidates): array
    {
        $type2SourceCloneCandidates = [];
        foreach ($type1SourceCloneCandidates as $type1SourceCloneCandidate) {
            $type2SourceCloneCandidates[] = $this->createFromType1SCC($type1SourceCloneCandidate);
        }

        return $this->type2SourceCloneCandidatesMerger->merge($type2SourceCloneCandidates);
    }

    private function createFromType1SCC(Type1SourceCloneCandidate $type1SourceCloneCandidate): Type2SourceCloneCandidate
    {
        return Type2SourceCloneCandidate::create(
            $this->tokenSequenceNormalizer->normalizeLevel2($type1SourceCloneCandidate->getTokenSequence()),
            $type1SourceCloneCandidate->getMethodsCollection(),
        );
    }
}