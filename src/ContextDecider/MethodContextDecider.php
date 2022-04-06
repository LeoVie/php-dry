<?php

namespace App\ContextDecider;

use App\Factory\TokenSequenceFactory;
use App\Model\Method\Method;
use LeoVie\PhpTokenNormalize\Service\TokenSequenceNormalizer;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;

class MethodContextDecider
{
    public function __construct(
        private TokenSequenceFactory    $tokenSequenceFactory,
        private TokenSequenceNormalizer $tokenSequenceNormalizer,
    ) {
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
        $returnType = $method->getParsedMethod()->getReturnType();

        if (!($returnType instanceof Identifier || $returnType instanceof Name)) {
            return false;
        }

        return $returnType->isSpecialClassName();
    }
}
