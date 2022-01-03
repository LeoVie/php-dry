<?php

declare(strict_types=1);

namespace App\Factory\SourceCloneCandidate;

use App\Collection\MethodsCollection;
use App\ContextDecider\MethodContextDecider;
use App\Exception\CollectionCannotBeEmpty;
use App\Exception\NoParamRequestForParamType;
use App\Factory\TokenSequenceFactory;
use App\Model\Method\MethodSignature;
use App\Model\Method\MethodSignatureGroup;
use App\Model\RunResult\RunResultSet;
use App\Model\SourceCloneCandidate\SourceCloneCandidate;
use App\Model\SourceCloneCandidate\Type4SourceCloneCandidate;
use LeoVie\PhpMethodRunner\Exception\CommandFailed;
use LeoVie\PhpMethodRunner\Model\Method;
use LeoVie\PhpMethodRunner\Model\MethodResult;
use LeoVie\PhpMethodRunner\Model\MethodRunRequest;
use LeoVie\PhpMethodRunner\Run\MethodRunner;
use LeoVie\PhpParamGenerator\Exception\NoParamGeneratorFoundForParamRequest;
use LeoVie\PhpParamGenerator\Model\Param\Param;
use LeoVie\PhpParamGenerator\Model\Param\ParamList\ParamList;
use LeoVie\PhpParamGenerator\Model\Param\ParamList\ParamListSet;
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
     * @throws FilesystemException
     * @throws NoParamGeneratorFoundForParamRequest
     */
    public function createMultipleByRunningMethods(array $methodSignatureGroups): array
    {
        $sourceCloneCandidates = [];

        foreach ($methodSignatureGroups as $methodSignatureGroup) {
            $signature = $methodSignatureGroup->getMethodsCollection()->getFirst()->getMethodSignature();

            try {
                $paramRequests = $this->createParamRequests($signature);
            } catch (NoParamRequestForParamType) {
                continue;
            }

            $paramListSet = $this->createParamListSet($paramRequests, 5);

            /** @var array<RunResultSet[]> $runResultSetsArray */
            $runResultSetsArray = [];
            foreach ($methodSignatureGroup->getMethodsCollection()->getAll() as $method) {
                if ($this->methodContextDecider->requiresClassContext($method)) {
                    continue;
                }

                try {
                    $methodResults = $this->runMethodMultipleTimes($method, $paramListSet);
                } catch (CommandFailed) {
                    continue;
                }

                $runResultSet = RunResultSet::create($method, $paramListSet, $methodResults);
                $runResultSetsArray[$runResultSet->hash()][] = $runResultSet;
            }

            array_push($sourceCloneCandidates, ...$this->createSourceCloneCandidatesForRunResultSetsArray($runResultSetsArray));
        }

        return $sourceCloneCandidates;
    }

    /** @throws NoParamRequestForParamType */
    private function createParamRequests(MethodSignature $methodSignature): array
    {
        $paramRequests = [];
        foreach ($methodSignature->getParamTypes() as $paramType) {
            $paramRequests[] = $this->createParamRequest($paramType);
        }

        return $paramRequests;
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

    /** @throws NoParamGeneratorFoundForParamRequest */
    private function createParamListSet(array $paramRequests, int $length): ParamListSet
    {
        $paramListSetRequest = ParamListSetRequest::create(
            ParamListRequest::create($paramRequests),
            $length
        );

        return $this->paramGeneratorService->generate($paramListSetRequest);
    }

    /**
     * @throws FilesystemException
     * @throws CommandFailed
     */
    private function runMethodMultipleTimes(\App\Model\Method\Method $method, ParamListSet $paramListSet): array
    {
        return array_map(
            fn(ParamList $paramList): MethodResult => $this->runMethod($method, $paramList),
            $paramListSet->getParamLists()
        );
    }

    /**
     * @throws FilesystemException
     * @throws CommandFailed
     */
    private function runMethod(\App\Model\Method\Method $method, ParamList $paramList): MethodResult
    {
        $methodRunRequest = MethodRunRequest::create(
            Method::create(
                $method->getName(),
                $this->tokenSequenceNormalizer->normalizeLevel4($this->tokenSequenceFactory->createFromMethod($method))->toCode()
            ),
            array_map(fn(Param $p): mixed => $p->flatten(), $paramList->getParams())
        );

        return $this->methodRunner->run($methodRunRequest);
    }

    /**
     * @param array<RunResultSet[]> $runResultSetsArray
     *
     * @return SourceCloneCandidate[]
     * @throws CollectionCannotBeEmpty
     */
    private function createSourceCloneCandidatesForRunResultSetsArray(array $runResultSetsArray): array
    {
        return array_values(
            array_map(
                fn(array $runResultSets): Type4SourceCloneCandidate => $this->createSourceCloneCandidateForRunResultSets($runResultSets),
                $runResultSetsArray
            )
        );
    }

    /**
     * @param RunResultSet[] $runResultSets
     *
     * @throws CollectionCannotBeEmpty
     */
    private function createSourceCloneCandidateForRunResultSets(array $runResultSets): Type4SourceCloneCandidate
    {
        $methods = array_map(fn(RunResultSet $rrs): \App\Model\Method\Method => $rrs->getMethod(), $runResultSets);

        return Type4SourceCloneCandidate::create(MethodsCollection::create(...$methods));
    }
}