<?php

declare(strict_types=1);

namespace App\Parse\Parser;

use App\Parse\NodeVisitor\ExtractFunctionsNodeVisitor;
use App\Parse\LineAndColumnLexerWrapper;
use App\Service\FileSystem;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Safe\Exceptions\FilesystemException;

class FunctionsParser
{
    public function __construct(
        private ParserFactory               $parserFactory,
        private NodeTraverser               $nodeTraverser,
        private ExtractFunctionsNodeVisitor $extractFunctionsNodeVisitor,
        private FileSystem                  $fileSystem,
        private LineAndColumnLexerWrapper   $lineAndColumnLexerWrapper
    )
    {
    }

    /**
     * @return Node\Stmt\Function_[]
     * @throws FilesystemException
     */
    public function extractFunctions(string $filepath): array
    {
        $this->extractFunctionsNodeVisitor = $this->extractFunctionsNodeVisitor->reset();

        $parser = $this->parserFactory->create(ParserFactory::PREFER_PHP7, $this->lineAndColumnLexerWrapper->getLexer());

        /** @var Node[] $ast */
        $ast = $parser->parse($this->fileSystem->readFile($filepath));

        $this->nodeTraverser->addVisitor($this->extractFunctionsNodeVisitor);
        $this->nodeTraverser->traverse($ast);

        return $this->extractFunctionsNodeVisitor->getFunctions();
    }
}