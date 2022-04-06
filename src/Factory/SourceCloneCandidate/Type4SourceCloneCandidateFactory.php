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
use LeoVie\PhpParamGenerator\Model\ParamRequest\FloatRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\IntRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\ParamList\ParamListRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\ParamList\ParamListSetRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\ParamRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\StringRequest;
use LeoVie\PhpParamGenerator\Service\ParamGeneratorService;
use LeoVie\PhpTokenNormalize\Service\TokenSequenceNormalizer;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\AggregatedType;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\String_;
use Safe\Exceptions\FilesystemException;

class Type4SourceCloneCandidateFactory
{
    public function __construct(
        private ParamGeneratorService   $paramGeneratorService,
        private MethodRunner            $methodRunner,
        private TokenSequenceFactory    $tokenSequenceFactory,
        private TokenSequenceNormalizer $tokenSequenceNormalizer,
        private MethodContextDecider    $methodContextDecider,
        private TypeResolver            $typeResolver,
    )
    {
    }

    /**
     * @param iterable<MethodSignatureGroup> $methodSignatureGroups
     *
     * @return Type4SourceCloneCandidate[]
     *
     * @throws CollectionCannotBeEmpty
     * @throws FilesystemException
     * @throws NoParamGeneratorFoundForParamRequest
     */
    public function createMultipleByRunningMethods(iterable $methodSignatureGroups): array
    {
        $sourceCloneCandidates = [];

        foreach ($methodSignatureGroups as $methodSignatureGroup) {
            $signature = $methodSignatureGroup->getMethodsCollection()->getFirst()->getMethodSignature();

            try {
                $paramRequests = $this->createParamRequests($signature);
            } catch (NoParamRequestForParamType $e) {
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

            array_push($sourceCloneCandidates, ...$this->createMultipleForRunResultSetsArray($runResultSetsArray));
        }

        return $sourceCloneCandidates;
    }

    /**
     * @return ParamRequest[]
     *
     * @throws NoParamRequestForParamType
     */
    private function createParamRequests(MethodSignature $methodSignature): array
    {
        $paramRequests = [];
        foreach ($methodSignature->getParamTypes() as $paramType) {
            $paramRequests[] = $this->createParamRequest($this->typeResolver->resolve($paramType));
        }

        return $paramRequests;
    }

    /** @throws NoParamRequestForParamType */
    private function createParamRequest(Type $paramType): ParamRequest
    {
        if (
            $paramType instanceof Array_
            || $paramType instanceof Iterable_
        ) {
            // TODO: make configurable
            $arrayLength = 3;

            $arrayTypeRequests = [];

            for ($i = 0; $i < $arrayLength; $i++) {
                $arrayTypeRequests[] = $this->createParamRequest($paramType->getValueType());
            }

            return ArrayRequest::create($arrayTypeRequests);
        }

        if ($paramType instanceof AggregatedType) {
            $randomPickedType = $paramType->get(rand(0, count($paramType->getIterator()) - 1));

            if ($randomPickedType === null) {
                throw NoParamRequestForParamType::create('null');
            }

            return $this->createParamRequest($randomPickedType);
        }

        return match (true) {
            is_a($paramType, String_::class) => StringRequest::create(),
            is_a($paramType, Integer::class) => IntRequest::create(),
            is_a($paramType, Float_::class) => FloatRequest::create(),
//            is_a($resolvedParamType, Boolean::class) => BooleanRequest::create(),
            default => throw NoParamRequestForParamType::create($paramType->__toString())
        };
    }

    /**
     * @param ParamRequest[] $paramRequests
     *
     * @throws NoParamGeneratorFoundForParamRequest
     */
    private function createParamListSet(array $paramRequests, int $length): ParamListSet
    {
        $paramListSetRequest = ParamListSetRequest::create(
            ParamListRequest::create($paramRequests),
            $length
        );

        return $this->paramGeneratorService->generate($paramListSetRequest);
    }

    /**
     * @return MethodResult[]
     * @throws CommandFailed
     *
     * @throws FilesystemException
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
     * @return Type4SourceCloneCandidate[]
     * @throws CollectionCannotBeEmpty
     */
    private function createMultipleForRunResultSetsArray(array $runResultSetsArray): array
    {
        return array_values(
            array_map(
                fn(array $runResultSets): Type4SourceCloneCandidate => $this->createForRunResultSets($runResultSets),
                $runResultSetsArray
            )
        );
    }

    /**
     * @param RunResultSet[] $runResultSets
     *
     * @throws CollectionCannotBeEmpty
     */
    private function createForRunResultSets(array $runResultSets): Type4SourceCloneCandidate
    {
        $methods = array_map(fn(RunResultSet $rrs): \App\Model\Method\Method => $rrs->getMethod(), $runResultSets);

        return Type4SourceCloneCandidate::create(MethodsCollection::create(...$methods));
    }
}
