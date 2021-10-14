<?php

declare(strict_types=1);

namespace App\Tests\Unit\Parse\Parser;

use App\Parse\Parser\MethodsParser;
use App\Parse\NodeVisitor\ExtractMethodsNodeVisitor;
use App\Parse\LineAndColumnLexerWrapper;
use App\Service\FileSystem;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;

class ClassMethodsParserTest extends TestCase
{
    public function testExtractClassMethods(): void
    {
        $parser = $this->createMock(Parser::class);
        $parser->method('parse')->willReturn([]);

        $parserFactory = $this->createMock(ParserFactory::class);
        $parserFactory->method('create')->willReturn($parser);

        $nodeTraverser = $this->createMock(NodeTraverser::class);
        $nodeTraverser->method('addVisitor');
        $nodeTraverser->method('traverse');

        $extractClassMethodsNodeVisitor = $this->createMock(ExtractMethodsNodeVisitor::class);
        $extractClassMethodsNodeVisitor->method('reset')->willReturnSelf();
        $extractClassMethodsNodeVisitor->method('getMethods')->willReturn(['methods']);

        $fileSystem = $this->createMock(FileSystem::class);
        $fileSystem->method('readFile')->willReturn('');

        $lineAndColumnLexerWrapper = $this->createMock(LineAndColumnLexerWrapper::class);

        $classMethodsParser = new MethodsParser(
            $parserFactory,
            $nodeTraverser,
            $extractClassMethodsNodeVisitor,
            $fileSystem,
            $lineAndColumnLexerWrapper
        );

        self::assertSame(['methods'], $classMethodsParser->extractMethods(''));
    }
}