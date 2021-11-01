<?php

declare(strict_types=1);

namespace App\CloneDetection;

use App\Collection\MethodsCollection;
use App\Exception\NoParamRequestForParamType;
use App\Model\SourceClone\SourceClone;
use LeoVie\PhpParamGenerator\Model\Param\Param;
use LeoVie\PhpParamGenerator\Model\ParamRequest\ArrayRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\IntRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\ParamList\ParamListRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\ParamList\ParamListSetRequest;
use LeoVie\PhpParamGenerator\Model\ParamRequest\StringRequest;
use LeoVie\PhpParamGenerator\Service\ParamGeneratorService;

class Type4CloneDetector
{
    public function __construct(private ParamGeneratorService $paramGeneratorService)
    {
    }

    /**
     * @param MethodsCollection[] $methodsGroupedBySignatures
     *
     * @return SourceClone[]
     */
    public function detect(array $methodsGroupedBySignatures): array
    {
        foreach ($methodsGroupedBySignatures as $methodsCollection) {
            $signature = $methodsCollection->getFirst()->getMethodSignature();

            $paramRequests = [];
            foreach ($signature->getParamTypes() as $paramType) {
                $paramRequests[] = match($paramType) {
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

            print($paramListSetRequest . "\n");

            $paramListSet = $this->paramGeneratorService->generate($paramListSetRequest);

            print($paramListSet . "\n\n");
        }

        throw new \Exception('not implemented');
    }
}