<?php

declare(strict_types=1);

namespace App\Tests\Unit\Parse\NodeVisitor;

use App\Parse\NodeVisitor\ExtractMethodsNodeVisitor;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPUnit\Framework\TestCase;

class ExtractMethodsNodeVisitorTest extends TestCase
{
    /**
     * @param ClassMethod[] $expected
     * @param Node[] $nodes
     *
     * @dataProvider enterNodeAndGetMethodsProvider
     */
    public function testEnterNodeAndGetMethods(array $expected, array $nodes): void
    {
        $extractClassMethodsNodeVisitor = new ExtractMethodsNodeVisitor();

        foreach ($nodes as $node) {
            $extractClassMethodsNodeVisitor->enterNode($node);
        }

        self::assertSame($expected, $extractClassMethodsNodeVisitor->getMethods());
    }

    public function enterNodeAndGetMethodsProvider(): \Generator
    {
        yield 'no nodes' => [
            'expected' => [],
            'nodes' => [],
        ];

        yield 'no ClassMethods' => [
            'expected' => [],
            'nodes' => [
                $this->createMock(Node::class),
                $this->createMock(Node::class),
            ],
        ];

        $nodes = [
            $this->createMock(ClassMethod::class),
            $this->createMock(ClassMethod::class),
        ];
        yield 'only ClassMethods' => [
            'expected' => $nodes,
            'nodes' => $nodes,
        ];

        $nodes = [
            $this->createMock(ClassMethod::class),
            $this->createMock(Node::class),
            $this->createMock(Function_::class),
        ];
        yield 'mixed Nodes' => [
            'expected' => [$nodes[0], $nodes[2]],
            'nodes' => $nodes,
        ];
    }

    public function testReset(): void
    {
        $extractClassMethodsNodeVisitor = new ExtractMethodsNodeVisitor();

        $nodes = [
            $this->createMock(ClassMethod::class),
            $this->createMock(ClassMethod::class),
        ];
        foreach ($nodes as $node) {
            $extractClassMethodsNodeVisitor->enterNode($node);
        }

        $extractClassMethodsNodeVisitor = $extractClassMethodsNodeVisitor->reset();

        self::assertSame([], $extractClassMethodsNodeVisitor->getMethods());
    }
}