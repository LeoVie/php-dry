<?php

declare(strict_types=1);

namespace App\Factory\SourceCloneCandidate;

use App\Collection\MethodsCollection;
use App\Configuration\Configuration;
use App\ContextDecider\MethodContextDecider;
use App\Exception\CollectionCannotBeEmpty;
use App\Exception\NoParamRequestForParamType;
use App\Factory\TokenSequenceFactory;
use App\Model\ClassModel\ClassModel;
use App\Model\Method\Method;
use App\Model\Method\MethodSignature;
use App\Model\Method\MethodSignatureGroup;
use App\Model\RunResult\RunResultSet;
use App\Model\SourceCloneCandidate\Type4SourceCloneCandidate;
use LeoVie\PhpMethodRunner\Exception\CommandFailed;
use LeoVie\PhpMethodRunner\Model\ClassData;
use LeoVie\PhpMethodRunner\Model\MethodData;
use LeoVie\PhpMethodRunner\Model\MethodResult;
use LeoVie\PhpMethodRunner\Model\MethodRunRequestWithAutoloading;
use LeoVie\PhpMethodRunner\Run\MethodRunner;
use LeoVie\PhpParamGenerator\Exception\NoParamGeneratorFoundForParamRequest;
use LeoVie\PhpParamGenerator\Model\Param\Param;
use LeoVie\PhpParamGenerator\Model\Param\ParamList\ParamList;
use LeoVie\PhpParamGenerator\Model\Param\ParamList\ParamListSet;
use LeoVie\PhpParamGenerator\Model\ParamRequest\ArrayRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\BoolRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\FloatRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\IntRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\NullRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\ObjectRequest;
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
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\Object_;
use Safe\Exceptions\FilesystemException;

class Type4SourceCloneCandidateFactory
{
    private Configuration $configuration;

    /** @var array<ClassModel> */
    private array $constructableClasses = [];

    public function __construct(
        private ParamGeneratorService   $paramGeneratorService,
        private MethodRunner            $methodRunner,
        private TokenSequenceFactory    $tokenSequenceFactory,
        private TokenSequenceNormalizer $tokenSequenceNormalizer,
        private MethodContextDecider    $methodContextDecider,
        private TypeResolver            $typeResolver
    )
    {
    }

    /**
     * @param iterable<MethodSignatureGroup> $methodSignatureGroups
     * @param array<ClassModel> $constructableClasses
     *
     * @return Type4SourceCloneCandidate[]
     *
     * @throws CollectionCannotBeEmpty
     * @throws FilesystemException
     * @throws NoParamGeneratorFoundForParamRequest
     */
    public function createMultipleByRunningMethods(iterable $methodSignatureGroups, array $constructableClasses): array
    {
        $this->configuration = Configuration::instance();
        $this->constructableClasses = $constructableClasses;

        $sourceCloneCandidates = [];

        foreach ($methodSignatureGroups as $methodSignatureGroup) {
            $signature = $methodSignatureGroup->getMethodsCollection()->getFirst()->getMethodSignature();

            try {
                $paramRequests = $this->createParamRequests($signature);
            } catch (NoParamRequestForParamType $e) {
                continue;
            }

            // TODO: make configurable
            $paramListSetLength = 5;
            $paramListSet = $this->createParamListSet($paramRequests, $paramListSetLength);

            /** @var array<RunResultSet[]> $runResultSetsArray */
            $runResultSetsArray = [];
            foreach ($methodSignatureGroup->getMethodsCollection()->getAll() as $method) {

                if ($this->methodContextDecider->requiresClassContext($method)) {
                    continue;
                }

                try {
                    $methodResults = $this->runMethodMultipleTimes($method, $paramListSet);
                } catch (CommandFailed $e) {
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
            $paramRequest = $this->createParamRequest($this->typeResolver->resolve($paramType));

            $paramRequests[] = $paramRequest;
        }

        return $paramRequests;
    }

    /** @throws NoParamRequestForParamType */
    private function createParamRequest(Type $paramType): ParamRequest
    {
        return match (true) {
            is_a($paramType, Array_::class),
            is_a($paramType, Iterable_::class) => $this->createArrayRequest($paramType),
            is_a($paramType, AggregatedType::class) => $this->createParamRequestForAggregatedType($paramType),
            is_a($paramType, String_::class) => StringRequest::create(),
            is_a($paramType, Integer::class) => IntRequest::create(),
            is_a($paramType, Float_::class) => FloatRequest::create(),
            is_a($paramType, Boolean::class) => BoolRequest::create(),
            is_a($paramType, Null_::class) => NullRequest::create(),
            is_a($paramType, Object_::class) => $this->createParamRequestForObject($paramType),
            default => throw NoParamRequestForParamType::create($paramType->__toString(), $paramType::class)
        };
    }

    /** @throws NoParamRequestForParamType */
    private function createArrayRequest(Iterable_|Array_ $paramType): ArrayRequest
    {
        // TODO: make configurable
        $arrayLength = 3;

        $arrayTypeRequests = [];

        for ($i = 0; $i < $arrayLength; $i++) {
            $arrayTypeRequests[] = $this->createParamRequest($paramType->getValueType());
        }

        return ArrayRequest::create($arrayTypeRequests);
    }

    /** @throws NoParamRequestForParamType */
    private function createParamRequestForAggregatedType(AggregatedType $paramType): ParamRequest
    {
        $randomPickedType = $paramType->get(rand(0, count($paramType->getIterator()) - 1));

        if ($randomPickedType === null) {
            throw NoParamRequestForParamType::create('null', 'null');
        }

        return $this->createParamRequest($randomPickedType);
    }

    /** @throws NoParamRequestForParamType */
    private function createParamRequestForObject(Object_ $paramType): ParamRequest
    {
        $class = $paramType->getFqsen();

        if ($class === null) {
            throw NoParamRequestForParamType::create('object', 'object');
        }

        /** @var class-string $classFQN */
        $classFQN = $class->__toString();

        $classIsConstructable = array_key_exists($classFQN, $this->constructableClasses);

        if (!$classIsConstructable) {
            throw NoParamRequestForParamType::create($paramType->__toString(), $classFQN);
        }

        $classModel = $this->constructableClasses[$classFQN];

        return ObjectRequest::create(
            $this->configuration->getBootstrapScriptPath(),
            $classFQN,
            $this->createParamRequests($classModel->getConstructorSignature())
        );
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
    private function runMethodMultipleTimes(Method $method, ParamListSet $paramListSet): array
    {
        $results = [];

        foreach ($paramListSet->getParamLists() as $paramList) {
            $orderedParams = array_combine($method->getMethodSignature()->getParamsOrder(), $paramList->getParams());
            ksort($orderedParams);
            $orderedParamList = ParamList::create($orderedParams);

            $results[] = $this->runMethod($method, $orderedParamList);
        }

        return $results;
    }

    /**
     * @throws FilesystemException
     * @throws CommandFailed
     */
    private function runMethod(Method $method, ParamList $paramList): MethodResult
    {
        $methodRunRequest = MethodRunRequestWithAutoloading::create(
            MethodData::create(
                $method->getName(),
                $this->tokenSequenceNormalizer->normalizeLevel4($this->tokenSequenceFactory->createFromMethod($method))->toCode()
            ),
            array_map(fn(Param $p): mixed => $p->flatten(), $paramList->getParams()),
            ClassData::create(
                $method->getClassFQN()
            ),
            // TODO: generate random class constructor params
            [],
            \Safe\realpath($this->configuration->getBootstrapScriptPath())
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
        $methods = array_map(fn(RunResultSet $rrs): Method => $rrs->getMethod(), $runResultSets);

        return Type4SourceCloneCandidate::create(MethodsCollection::create(...$methods));
    }
}
