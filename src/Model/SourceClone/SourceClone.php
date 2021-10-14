<?php

declare(strict_types=1);

namespace App\Model\SourceClone;

use App\Collection\MethodsCollection;
use App\Model\Method\Method;
use Safe\Exceptions\StringsException;
use Stringable;

class SourceClone implements Stringable
{
    public const TYPE_1 = 'TYPE_1';
    public const TYPE_2 = 'TYPE_2';
    public const TYPE_3 = 'TYPE_3';
    public const TYPE_4 = 'TYPE_4';

    private function __construct(
        private string            $type,
        private MethodsCollection $methodsCollection,
    )
    {
    }

    public static function createType1(MethodsCollection $methodsCollection): self
    {
        return new self(self::TYPE_1, $methodsCollection);
    }

    public static function createType2(MethodsCollection $methodsCollection): self
    {
        return new self(self::TYPE_2, $methodsCollection);
    }

    public static function createType3(MethodsCollection $methodsCollection): self
    {
        return new self(self::TYPE_3, $methodsCollection);
    }

    public static function createType4(MethodsCollection $methodsCollection): self
    {
        return new self(self::TYPE_4, $methodsCollection);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMethodsCollection(): MethodsCollection
    {
        return $this->methodsCollection;
    }

    /** @throws StringsException */
    public function __toString(): string
    {
        return \Safe\sprintf(
            "CLONE: Type: %s, Methods: \n\t%s",
            $this->getType(),
            join("\n\t", array_map(fn(Method $m) => $m->__toString(), $this->getMethodsCollection()->getAll()))
        );
    }
}