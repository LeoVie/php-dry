<?php

declare(strict_types=1);

namespace App\Factory\SourceCloneCandidate;

use App\Collection\MethodsCollection;
use App\ContextDecider\MethodContextDecider;
use App\Exception\CollectionCannotBeEmpty;
use App\Exception\NoParamRequestForParamType;
use App\Factory\TokenSequenceFactory;
use App\Model\Method\MethodSignatureGroup;
use App\Model\RunResult\RunResultSet;
use App\Model\SourceCloneCandidate\Type4SourceCloneCandidate;
use LeoVie\PhpMethodRunner\Exception\CommandFailed;
use LeoVie\PhpMethodRunner\Model\Method;
use LeoVie\PhpMethodRunner\Model\MethodRunRequest;
use LeoVie\PhpMethodRunner\Run\MethodRunner;
use LeoVie\PhpParamGenerator\Exception\NoParamGeneratorFoundForParamRequest;
use LeoVie\PhpParamGenerator\Model\Param\Param;
use LeoVie\PhpParamGenerator\Model\ParamRequest\ArrayRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\IntRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\ParamList\ParamListRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\ParamList\ParamListSetRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\ParamRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\StringRequest;
use LeoVie\PhpParamGenerator\Service\ParamGeneratorService;
use LeoVie\PhpTokenNormalize\Service\TokenSequenceNormalizer;
use Safe\Exceptions\FilesystemException;

class Type4SourceCloneCandidateFactory
{
    public function __construct(
        private ParamGeneratorService   $paramGeneratorService,
        private MethodRunner            $methodRunner,
        private TokenSequenceFactory    $tokenSequenceFactory,
        private TokenSequenceNormalizer $tokenSequenceNormalizer,
        private MethodContextDecider    $methodContextDecider,
    )
    {
    }

    /**
     * @param MethodSignatureGroup[] $methodSignatureGroups
     *
     * @return Type4SourceCloneCandidate[]
     *
     * @throws CollectionCannotBeEmpty
     * @throws NoParamGeneratorFoundForParamRequest
     * @throws FilesystemException
     */
    public function createMultiple(array $methodSignatureGroups): array
    {
        $sourceCloneCandidates = [];

        foreach ($methodSignatureGroups as $methodSignatureGroup) {
            $signature = $methodSignatureGroup->getMethodsCollection()->getFirst()->getMethodSignature();

            $paramRequests = [];
            foreach ($signature->getParamTypes() as $paramType) {
                try {
                    $paramRequests[] = $this->createParamRequest($paramType);
                } catch (NoParamRequestForParamType) {
                    continue 2;
                }
            }

            $paramListSetRequest = ParamListSetRequest::create(
                ParamListRequest::create($paramRequests),
                5
            );

            $paramListSet = $this->paramGeneratorService->generate($paramListSetRequest);

            /** @var array<RunResultSet[]> $runResultSetsArray */
            $runResultSetsArray = [];
            foreach ($methodSignatureGroup->getMethodsCollection()->getAll() as $msgMethod) {
                if ($this->methodContextDecider->requiresClassContext($msgMethod)) {
                    continue;
                }

                $methodResults = [];
                foreach ($paramListSet->getParamLists() as $paramList) {
                    $methodRunRequest = MethodRunRequest::create(
                        Method::create(
                            $msgMethod->getName(),
                            $this->tokenSequenceNormalizer->normalizeLevel4($this->tokenSequenceFactory->createFromMethod($msgMethod))->toCode()
                        ),
                        array_map(fn(Param $p): mixed => $p->flatten(), $paramList->getParams())
                    );

                    try {
                        $methodResults[] = $this->methodRunner->run($methodRunRequest);
                    } catch (CommandFailed) {
                        continue 2;
                    }
                }

                $runResultSet = RunResultSet::create($msgMethod, $paramListSet, $methodResults);
                $runResultSetsArray[$runResultSet->hash()][] = $runResultSet;
            }

            foreach ($runResultSetsArray as $runResultSets) {
                $methods = array_map(fn(RunResultSet $rrs): \App\Model\Method\Method => $rrs->getMethod(), $runResultSets);
                $sourceCloneCandidates[] = Type4SourceCloneCandidate::create(MethodsCollection::create(...$methods));
            }
        }

        return $sourceCloneCandidates;
    }

    /** @throws NoParamRequestForParamType */
    private function createParamRequest(string $paramType): ParamRequest
    {
        return match ($paramType) {
            'int' => IntRequest::create(),
            'string' => StringRequest::create(),
            'array' => ArrayRequest::create([IntRequest::create(), IntRequest::create(), IntRequest::create()]),
            default => throw NoParamRequestForParamType::create($paramType)
        };
    }
}