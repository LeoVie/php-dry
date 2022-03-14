<?php

declare(strict_types=1);

namespace App\Model\RunResult;

use App\Model\Method\Method;
use LeoVie\PhpMethodRunner\Model\MethodResult;
use LeoVie\PhpParamGenerator\Model\Param\ParamList\ParamListSet;

class RunResultSet
{
    /** @param MethodResult[] $methodResults */
    private function __construct(
        private Method       $method,
        private ParamListSet $paramListSet,
        private array        $methodResults
    )
    {
    }

    /** @param MethodResult[] $methodResults */
    public static function create(Method $method, ParamListSet $paramListSet, array $methodResults): self
    {
        return new self($method, $paramListSet, $methodResults);
    }

    public function getMethod(): Method
    {
        return $this->method;
    }

    public function getParamListSet(): ParamListSet
    {
        return $this->paramListSet;
    }

    /** @return MethodResult[] */
    public function getMethodResults(): array
    {
        return $this->methodResults;
    }

    public function hash(): string
    {
        return $this->paramListSet->hash()
            . '=>'
            . join('&', array_map(fn(MethodResult $mr): string => serialize($mr), $this->methodResults));
    }
}