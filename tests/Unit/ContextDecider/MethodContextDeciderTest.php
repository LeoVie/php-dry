<?php

namespace App\Tests\Unit\ContextDecider;

use App\ContextDecider\MethodContextDecider;
use App\Factory\TokenSequenceFactory;
use App\Model\CodePosition\CodePositionRange;
use App\Model\Method\Method;
use App\Model\Method\MethodSignature;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use LeoVie\PhpTokenNormalize\Service\TokenSequenceNormalizer;
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

        self::assertSame(
            $expected,
            (new MethodContextDecider($tokenSequenceFactory, $tokenSequenceNormalizer))->requiresClassContext($method)
        );
    }

    public function requiresClassContextProvider(): \Generator
    {
        yield 'body requires class context (contains $this)' => [
            'expected' => true,
            'method' => Method::create(
                $this->createMock(MethodSignature::class),
                'foo',
                'foo.php',
                $this->createMock(CodePositionRange::class),
                'function foo(): int { return $this->bar(); }',
                $this->createMock(ClassMethod::class)
            ),
        ];

        yield 'body requires class context (contains self::)' => [
            'expected' => true,
            'method' => Method::create(
                $this->createMock(MethodSignature::class),
                'foo',
                'foo.php',
                $this->createMock(CodePositionRange::class),
                'function foo(): int { return self::bar(); }',
                $this->createMock(ClassMethod::class)
            ),
        ];

        yield 'body requires class context (contains self ::)' => [
            'expected' => true,
            'method' => Method::create(
                $this->createMock(MethodSignature::class),
                'foo',
                'foo.php',
                $this->createMock(CodePositionRange::class),
                'function foo(): int { return self :: bar(); }',
                $this->createMock(ClassMethod::class)
            ),
        ];

        yield 'body requires class context (contains parent::)' => [
            'expected' => true,
            'method' => Method::create(
                $this->createMock(MethodSignature::class),
                'foo',
                'foo.php',
                $this->createMock(CodePositionRange::class),
                'function foo(): int { return parent::bar(); }',
                $this->createMock(ClassMethod::class)
            ),
        ];

        yield 'body requires class context (contains parent ::)' => [
            'expected' => true,
            'method' => Method::create(
                $this->createMock(MethodSignature::class),
                'foo',
                'foo.php',
                $this->createMock(CodePositionRange::class),
                'function foo(): int { return parent :: bar(); }',
                $this->createMock(ClassMethod::class)
            ),
        ];

        $parsedMethod = $this->createMock(ClassMethod::class);
        $returnType = $this->createMock(Identifier::class);
        $returnType->method('isSpecialClassName')->willReturn(true);
        $parsedMethod->method('getReturnType')->willReturn($returnType);
        yield 'return type requires class context (return type is Identifier)' => [
            'expected' => true,
            'method' => Method::create(
                $this->createMock(MethodSignature::class),
                'foo',
                'foo.php',
                $this->createMock(CodePositionRange::class),
                'function foo(): Foo { return 100; }',
                $parsedMethod
            ),
        ];

        $parsedMethod = $this->createMock(ClassMethod::class);
        $returnType = $this->createMock(Name::class);
        $returnType->method('isSpecialClassName')->willReturn(true);
        $parsedMethod->method('getReturnType')->willReturn($returnType);
        yield 'return type requires class context (return type is Name)' => [
            'expected' => true,
            'method' => Method::create(
                $this->createMock(MethodSignature::class),
                'foo',
                'foo.php',
                $this->createMock(CodePositionRange::class),
                'function foo(): Foo { return 100; }',
                $parsedMethod
            ),
        ];

        $parsedMethod = $this->createMock(ClassMethod::class);
        $returnType = $this->createMock(Name::class);
        $returnType->method('isSpecialClassName')->willReturn(false);
        $parsedMethod->method('getReturnType')->willReturn($returnType);
        yield 'does not require class context' => [
            'expected' => false,
            'method' => Method::create(
                $this->createMock(MethodSignature::class),
                'foo',
                'foo.php',
                $this->createMock(CodePositionRange::class),
                'function foo(): Foo { return 100; }',
                $parsedMethod
            ),
        ];
    }
}