<?php

namespace App\Tests\Unit\ContextDecider;

use App\ContextDecider\MethodContextDecider;
use App\Factory\TokenSequenceFactory;
use App\Model\CodePosition\CodePositionRange;
use App\Model\Method\Method;
use App\Model\Method\MethodSignature;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use LeoVie\PhpTokenNormalize\Service\TokenSequenceNormalizer;
use phpDocumentor\Reflection\TypeResolver;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PHPUnit\Framework\TestCase;

class MethodContextDeciderTest extends TestCase
{
    /** @dataProvider requiresClassContextProvider */
    public function testRequiresClassContext(bool $expected, Method $method): void
    {
        $tokenSequenceFactory = $this->createMock(TokenSequenceFactory::class);
        $tokenSequenceFactory->method('createFromMethod')->willReturn(TokenSequence::create([]));

        $normalizedTokenSequence = $this->createMock(TokenSequence::class);
        $normalizedTokenSequence->method('toCode')->willReturn($method->getContent());

        $tokenSequenceNormalizer = $this->createMock(TokenSequenceNormalizer::class);
        $tokenSequenceNormalizer->method('normalizeLevel4')->willReturnCallback(
            fn(TokenSequence $_) => $normalizedTokenSequence
        );

        $typeResolver = new TypeResolver();

        self::assertSame(
            $expected,
            (new MethodContextDecider($tokenSequenceFactory, $tokenSequenceNormalizer, $typeResolver))
                ->requiresClassContext($method)
        );
    }

    public function requiresClassContextProvider(): \Generator
    {
        yield 'body requires class context (contains $this)' => [
            'expected' => true,
            'method' => Method::create(
                MethodSignature::create([], [], 'int'),
                'foo',
                'foo.php',
                $this->createMock(CodePositionRange::class),
                'function foo(): int { return $this->bar(); }',
            ),
        ];

        yield 'body requires class context (contains self::)' => [
            'expected' => true,
            'method' => Method::create(
                MethodSignature::create([], [], 'int'),
                'foo',
                'foo.php',
                $this->createMock(CodePositionRange::class),
                'function foo(): int { return self::bar(); }',
            ),
        ];

        yield 'body requires class context (contains self ::)' => [
            'expected' => true,
            'method' => Method::create(
                MethodSignature::create([], [], 'int'),
                'foo',
                'foo.php',
                $this->createMock(CodePositionRange::class),
                'function foo(): int { return self :: bar(); }',
            ),
        ];

        yield 'body requires class context (contains parent::)' => [
            'expected' => true,
            'method' => Method::create(
                MethodSignature::create([], [], 'int'),
                'foo',
                'foo.php',
                $this->createMock(CodePositionRange::class),
                'function foo(): int { return parent::bar(); }',
            ),
        ];

        yield 'body requires class context (contains parent ::)' => [
            'expected' => true,
            'method' => Method::create(
                MethodSignature::create([], [], 'int'),
                'foo',
                'foo.php',
                $this->createMock(CodePositionRange::class),
                'function foo(): int { return parent :: bar(); }',
            ),
        ];

        $parsedMethod = $this->createMock(ClassMethod::class);
        $returnType = $this->createMock(Identifier::class);
        $returnType->method('isSpecialClassName')->willReturn(true);
        $parsedMethod->method('getReturnType')->willReturn($returnType);
        yield 'return type requires class context (return type is class)' => [
            'expected' => true,
            'method' => Method::create(
                MethodSignature::create([], [], 'Foo'),
                'foo',
                'foo.php',
                $this->createMock(CodePositionRange::class),
                'function foo(): Foo { return 100; }',
            ),
        ];

        $parsedMethod = $this->createMock(ClassMethod::class);
        $returnType = $this->createMock(Name::class);
        $returnType->method('isSpecialClassName')->willReturn(false);
        $parsedMethod->method('getReturnType')->willReturn($returnType);
        yield 'does not require class context (no special return type)' => [
            'expected' => false,
            'method' => Method::create(
                MethodSignature::create([], [], 'int'),
                'foo',
                'foo.php',
                $this->createMock(CodePositionRange::class),
                'function foo(): int { return 100; }',
            ),
        ];

        $parsedMethod = $this->createMock(ClassMethod::class);
        $parsedMethod->method('getReturnType')->willReturn(null);
        yield 'does not require class context (returns nothing)' => [
            'expected' => false,
            'method' => Method::create(
                MethodSignature::create([], [], 'void'),
                'foo',
                'foo.php',
                $this->createMock(CodePositionRange::class),
                'function foo(): void { $x = 100; }',
            ),
        ];
    }
}
