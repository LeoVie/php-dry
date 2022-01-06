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
    public function createMultiple(array $type1SourceCloneCandidates): array
    {
        return $this->type2SourceCloneCandidatesMerger->merge(
            array_map(
                function (Type1SourceCloneCandidate $scc): Type2SourceCloneCandidate {
                    return Type2SourceCloneCandidate::create(
                        $this->tokenSequenceNormalizer->normalizeLevel2($scc->getTokenSequence()),
                        $scc->getMethodsCollection(),
                    );
                }, $type1SourceCloneCandidates)
        );
    }
}