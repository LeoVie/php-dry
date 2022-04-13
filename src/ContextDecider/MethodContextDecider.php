<?php

namespace App\ContextDecider;

use App\Factory\TokenSequenceFactory;
use App\Model\Method\Method;
use LeoVie\PhpTokenNormalize\Service\TokenSequenceNormalizer;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\AggregatedType;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Never_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\Void_;

class MethodContextDecider
{
    public function __construct(
        private TokenSequenceFactory    $tokenSequenceFactory,
        private TokenSequenceNormalizer $tokenSequenceNormalizer,
        private TypeResolver            $typeResolver,
    )
    {
    }

    public function requiresClassContext(Method $method): bool
    {
        return (
            $this->bodyRequiresClassContext($method)
            || $this->returnTypeRequiresClassContext($method)
        );
    }

    private function bodyRequiresClassContext(Method $method): bool
    {
        $bodyClassContextEvidences = ['$this', 'self::', 'self ::', 'parent::', 'parent ::'];
        $methodCode = $this->tokenSequenceNormalizer->normalizeLevel4($this->tokenSequenceFactory->createFromMethod($method))->toCode();

        return $this->stringContainsAny($methodCode, $bodyClassContextEvidences);
    }

    /** @param string[] $needles */
    private function stringContainsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function returnTypeRequiresClassContext(Method $method): bool
    {
        $returnTypeRequiresClassContext = $this->typeRequiresClassContext(
            $this->typeResolver->resolve($method->getMethodSignature()->getReturnType())
        );

        return $returnTypeRequiresClassContext;
    }

    private function typeRequiresClassContext(Type $type): bool
    {
        if (
            $type instanceof Iterable_
            || $type instanceof Array_
        ) {
            return $this->iterableContainsClassContextType($type);
        }

        if ($type instanceof AggregatedType) {
            return $this->aggregatedTypeContainsClassContextType($type);
        }

        return !(
            is_a($type, String_::class)
            || is_a($type, Integer::class)
            || is_a($type, Float_::class)
            || is_a($type, Boolean::class)
            || is_a($type, Mixed_::class)
            || is_a($type, Never_::class)
            || is_a($type, Void_::class)
            // TODO: should we support callables -> requires changes in phpdocumentor/type-resolver?
//            || is_a($type, Callable_::class)
        );
    }

    private function iterableContainsClassContextType(Iterable_|Array_ $iterable): bool
    {
        if ($this->typeRequiresClassContext($iterable->getKeyType())) {
            return true;
        }

        return $this->typeRequiresClassContext($iterable->getValueType());
    }

    private function aggregatedTypeContainsClassContextType(AggregatedType $aggregatedType): bool
    {
        foreach ($aggregatedType as $type) {
            if ($this->typeRequiresClassContext($type)) {
                return true;
            }
        }

        return false;
    }
}
