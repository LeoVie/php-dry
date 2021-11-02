<?php

declare(strict_types=1);

namespace App\Factory\SourceCloneCandidate;

use App\Collection\MethodsCollection;
use App\Exception\CollectionCannotBeEmpty;
use App\Exception\NoParamRequestForParamType;
use App\Factory\TokenSequenceFactory;
use App\Model\Method\MethodSignatureGroup;
use App\Model\RunResult\RunResultSet;
use App\Model\SourceCloneCandidate\Type4SourceCloneCandidate;
use LeoVie\PhpMethodRunner\Configuration\Configuration;
use LeoVie\PhpMethodRunner\Model\Method;
use LeoVie\PhpMethodRunner\Model\MethodRunRequest;
use LeoVie\PhpMethodRunner\Run\MethodRunner;
use LeoVie\PhpParamGenerator\Exception\NoParamGeneratorFoundForParamRequest;
use LeoVie\PhpParamGenerator\Model\Param\Param;
use LeoVie\PhpParamGenerator\Model\ParamRequest\ArrayRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\IntRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\ParamList\ParamListRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\ParamList\ParamListSetRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\StringRequest;
use LeoVie\PhpParamGenerator\Service\ParamGeneratorService;
use LeoVie\PhpTokenNormalize\Service\TokenSequenceNormalizer;

class Type4SourceCloneCandidateFactory
{
    public function __construct(
        private ParamGeneratorService   $paramGeneratorService,
        private MethodRunner            $methodRunner,
        private TokenSequenceFactory    $tokenSequenceFactory,
        private TokenSequenceNormalizer $tokenSequenceNormalizer,
    )
    {
    }

    /**
     * @param MethodSignatureGroup[] $methodSignatureGroups
     *
     * @return Type4SourceCloneCandidate[]
     *
     * @throws CollectionCannotBeEmpty
     * @throws NoParamRequestForParamType
     * @throws NoParamGeneratorFoundForParamRequest
     */
    public function createMultiple(array $methodSignatureGroups): array
    {
        $sourceCloneCandidates = [];

        $methodRunnerConfiguration = Configuration::create(
            __DIR__ . '/../../../generated'
        );

        foreach ($methodSignatureGroups as $methodSignatureGroup) {
            $signature = $methodSignatureGroup->getMethodsCollection()->getFirst()->getMethodSignature();

            $paramRequests = [];
            foreach ($signature->getParamTypes() as $paramType) {
                $paramRequests[] = match ($paramType) {
                    'int' => IntRequest::create(),
                    'string' => StringRequest::create(),
                    'array' => ArrayRequest::create([IntRequest::create(), IntRequest::create(), IntRequest::create()]),
                    default => throw NoParamRequestForParamType::create($paramType)
                };
            }

            $paramListSetRequest = ParamListSetRequest::create(
                ParamListRequest::create($paramRequests),
                5
            );

            $paramListSet = $this->paramGeneratorService->generate($paramListSetRequest);

            /** @var array<RunResultSet[]> $runResultSetsArray */
            $runResultSetsArray = [];
            foreach ($methodSignatureGroup->getMethodsCollection()->getAll() as $msgMethod) {
                $methodResults = [];
                foreach ($paramListSet->getParamLists() as $paramList) {
                    $methodRunRequest = MethodRunRequest::create(
                        Method::create(
                            $msgMethod->getName(),
                            $this->tokenSequenceNormalizer->normalizeLevel4($this->tokenSequenceFactory->createFromMethod($msgMethod))->toCode()
                        ),
                        array_map(fn(Param $p): mixed => $p->flatten(), $paramList->getParams())
                    );

                    $methodResults[] = $this->methodRunner->run($methodRunRequest, $methodRunnerConfiguration);
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
}