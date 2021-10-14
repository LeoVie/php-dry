<?php

declare(strict_types=1);

namespace App\Parse\Parser;

use App\Parse\NodeVisitor\ExtractClassnameNodeVisitor;
use App\Service\FileSystem;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Safe\Exceptions\FilesystemException;

class ClassnameParser
{
    public function __construct(
        private ParserFactory               $parserFactory,
        private NodeTraverser               $nodeTraverser,
        private ExtractClassnameNodeVisitor $extractClassnameNodeVisitor,
        private FileSystem                  $fileSystem,
    )
    {
    }

    /**
     * @throws FilesystemException
     */
    public function extractClassname(string $filepath): ?string
    {
        $this->extractClassnameNodeVisitor = $this->extractClassnameNodeVisitor->reset();

        $parser = $this->parserFactory->create(ParserFactory::PREFER_PHP7);

        /** @var Node[] $ast */
        $ast = $parser->parse($this->fileSystem->readFile($filepath));

        $this->nodeTraverser->addVisitor($this->extractClassnameNodeVisitor);
        $this->nodeTraverser->traverse($ast);

        return $this->extractClassnameNodeVisitor->getClassname();
    }
}