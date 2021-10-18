<?php

declare(strict_types=1);

namespace App\Tests\Unit\Parse\Extractor;

use App\Parse\Converter\NodeTypeToStringConverter;
use App\Parse\Extractor\ReturnTypeExtractor;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPUnit\Framework\TestCase;

class ReturnTypeExtractorTest extends TestCase
{
    /** @dataProvider extractProvider */
    public function testExtract(string $expected, NodeTypeToStringConverter $nodeTypeToStringConverter, ClassMethod|Function_ $method): void
    {
        self::assertSame($expected, (new ReturnTypeExtractor($nodeTypeToStringConverter))->extract($method));
    }

    public function extractProvider(): \Generator
    {
        $nodeTypeToStringConverter = $this->createMock(NodeTypeToStringConverter::class);
        $nodeTypeToStringConverter->method('convert')->willReturnCallback(
            fn(Identifier $type): string => 'converted_' . $type->toString()
        );

        $method = $this->createMock(ClassMethod::class);
        $method->method('getReturnType')->willReturn(new Identifier('int'));

        yield [
            'expected' => 'converted_int',
            'nodeTypeToStringConverter' => $nodeTypeToStringConverter,
            'method' => $method,
        ];
    }
}