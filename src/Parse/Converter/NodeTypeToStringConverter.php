<?php

declare(strict_types=1);

namespace App\Parse\Converter;

use App\Exception\NodeTypeNotConvertable;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;
use Safe\Exceptions\StringsException;

class NodeTypeToStringConverter
{
    public const VOID_TYPE = 'void';

    /**
     * @throws NodeTypeNotConvertable
     * @throws StringsException
     */
    public function convert(null|Identifier|Name|ComplexType $type): string
    {
        return match (true) {
            $type === null => self::VOID_TYPE,
            $type instanceof Identifier => $type->name,
            $type instanceof Name => $type->toString(),
            $type instanceof UnionType => join('|', array_map(fn(Identifier|Name $x) => $this->convert($x), $type->types)),
            $type instanceof NullableType => '?' . $this->convert($type->type),
            default => throw NodeTypeNotConvertable::create($type::class),
        };
    }
}