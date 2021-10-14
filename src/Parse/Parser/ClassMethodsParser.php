<?php

declare(strict_types=1);

namespace App\Parse\Parser;

use App\Parse\NodeVisitor\ExtractClassMethodsNodeVisitor;
use App\Parse\LineAndColumnLexerWrapper;
use App\Service\FileSystem;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\Node\Stmt\ClassMethod;
use Safe\Exceptions\FilesystemException;

class ClassMethodsParser
{
    public function __construct(
        private ParserFactory                  $parserFactory,
        private NodeTraverser                  $nodeTraverser,
        private ExtractClassMethodsNodeVisitor $extractClassMethodsNodeVisitor,
        private FileSystem                     $fileSystem,
        private LineAndColumnLexerWrapper      $lineAndColumnLexerWrapper
    )
    {
    }

    /**
     * @return ClassMethod[]
     * @throws FilesystemException
     */
    public function extractClassMethods(string $filepath): array
    {
        $this->extractClassMethodsNodeVisitor = $this->extractClassMethodsNodeVisitor->reset();

        $parser = $this->parserFactory->create(ParserFactory::PREFER_PHP7, $this->lineAndColumnLexerWrapper->getLexer());

        /** @var Node[] $ast */
        $ast = $parser->parse($this->fileSystem->readFile($filepath));

        $this->nodeTraverser->addVisitor($this->extractClassMethodsNodeVisitor);
        $this->nodeTraverser->traverse($ast);

        return $this->extractClassMethodsNodeVisitor->getMethods();
    }
}