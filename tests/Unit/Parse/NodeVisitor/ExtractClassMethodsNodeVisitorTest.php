<?php

declare(strict_types=1);

namespace App\Tests\Unit\Parse\NodeVisitor;

use App\Parse\NodeVisitor\ExtractClassMethodsNodeVisitor;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPUnit\Framework\TestCase;

class ExtractClassMethodsNodeVisitorTest extends TestCase
{
    /**
     * @param ClassMethod[] $expected
     * @param Node[] $nodes
     *
     * @dataProvider enterNodeAndGetMethodsProvider
     */
    public function testEnterNodeAndGetMethods(array $expected, array $nodes): void
    {
        $extractClassMethodsNodeVisitor = new ExtractClassMethodsNodeVisitor();

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
            $this->createMock(ClassMethod::class),
        ];
        yield 'mixed Nodes' => [
            'expected' => [$nodes[0], $nodes[2]],
            'nodes' => $nodes,
        ];
    }

    public function testReset(): void
    {
        $extractClassMethodsNodeVisitor = new ExtractClassMethodsNodeVisitor();

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