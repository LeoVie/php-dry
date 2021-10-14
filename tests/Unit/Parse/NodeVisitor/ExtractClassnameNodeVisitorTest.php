<?php

declare(strict_types=1);

namespace App\Tests\Unit\Parse\NodeVisitor;

use App\Parse\NodeVisitor\ExtractClassnameNodeVisitor;
use Generator;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPUnit\Framework\TestCase;

class ExtractClassnameNodeVisitorTest extends TestCase
{
    /** @dataProvider enterNodeAndGetClassnameProvider */
    public function testEnterNodeAndGetClassname(?string $expected, array $nodes): void
    {
        $extractClassnameVisitor = new ExtractClassnameNodeVisitor();

        foreach ($nodes as $node) {
            $extractClassnameVisitor->enterNode($node);
        }

        self::assertSame($expected, $extractClassnameVisitor->getClassname());
    }

    public function enterNodeAndGetClassnameProvider(): Generator
    {
        yield 'no nodes' => [
            'expected' => null,
            'nodes' => [],
        ];

        yield 'no ClassLikes' => [
            'expected' => null,
            'nodes' => [
                $this->createMock(Node::class),
                $this->createMock(Node::class),
            ],
        ];

        yield 'ClassLike without name' => [
            'expected' => null,
            'nodes' => [
                $this->createMock(ClassLike::class),
            ],
        ];

        $nodes = [
            $this->createMock(ClassLike::class),
            $this->createMock(Node::class),
        ];
        $nodes[0]->name = $this->createMock(Node\Identifier::class);
        $nodes[0]->name->method('toString')->willReturn('TheClass');
        yield 'mixed Nodes' => [
            'expected' => 'TheClass',
            'nodes' => $nodes,
        ];
    }

    /** @dataProvider resetProvider */
    public function testReset(array $nodes): void
    {
        $extractClassnameVisitor = new ExtractClassnameNodeVisitor();

        foreach ($nodes as $node) {
            $extractClassnameVisitor->enterNode($node);
        }

        $extractClassnameVisitor = $extractClassnameVisitor->reset();

        self::assertNull($extractClassnameVisitor->getClassname());
    }

    public function resetProvider(): array
    {
        $nodes = [
            $this->createMock(ClassLike::class),
            $this->createMock(Node::class),
        ];
        $nodes[0]->name = $this->createMock(Node\Identifier::class);
        $nodes[0]->name->method('toString')->willReturn('TheClass');

        return [
            [$nodes],
        ];
    }
}