<?php

declare(strict_types=1);

namespace App\Service;

use App\Configuration\Configuration;
use App\Model\CodePosition\CodePosition;
use App\Model\CodePosition\CodePositionRange;
use App\Model\Method\Method;
use App\Model\Method\MethodSignature;
use App\ServiceFactory\CrawlerFactory;
use LeoVie\PhpFilesystem\Model\Boundaries;
use LeoVie\PhpFilesystem\Service\Filesystem;
use Safe\Exceptions\FilesystemException;
use Symfony\Component\DomCrawler\Crawler;

class FindMethodsInPathsService
{
    public function __construct(private Filesystem $filesystem)
    {
    }

    /**
     * @return array<Method>
     *
     * @throws FilesystemException
     */
    public function findAll(): array
    {
        $configuration = Configuration::instance();
        $reportPath = $configuration->getPhpDocumentorReportPath();
        $reportXmlFilepath = rtrim($reportPath, '/') . '/structure.xml';

        $crawler = CrawlerFactory::create(\Safe\file_get_contents($reportXmlFilepath));

        $directory = rtrim($configuration->getDirectory(), '/') . '/';

        return $this->findMethodsInProject($crawler->filter('project')->first(), $directory);
    }

    /** @return array<Method> */
    private function findMethodsInProject(Crawler $projectElement, string $directory): array
    {
        $methods = [];
        foreach ($projectElement->children() as $element) {
            if ($element->nodeName === 'file') {
                $methods = array_merge($methods, $this->findMethodsInFile(CrawlerFactory::create($element), $directory));
            }
        }

        return $methods;
    }

    /** @return array<Method> */
    private function findMethodsInFile(Crawler $fileElement, string $directory): array
    {
        $filepath = $directory . $fileElement->attr('path');

        $methods = [];
        foreach ($fileElement->children() as $element) {
            if ($element->nodeName === 'class') {
                $methods = array_merge($methods, $this->findMethodsInClass(CrawlerFactory::create($element), $filepath));
            }
        }

        return $methods;
    }

    /** @return array<Method> */
    private function findMethodsInClass(Crawler $classElement, string $filepath): array
    {
        $methods = [];

        foreach ($classElement->children() as $element) {
            if ($element->nodeName === 'method') {
                $methods[] = $this->buildMethod(CrawlerFactory::create($element), $filepath);
            }
        }

        return $methods;
    }

    private function buildMethod(Crawler $methodElement, string $filepath): Method
    {
        $codePositionRange = $this->buildCodePositionRange($methodElement);

        $methodSignature = $this->buildMethodSignature($methodElement);

        return Method::create(
            $methodSignature,
            $methodElement->filter('name')->first()->text(),
            $filepath,
            $codePositionRange,
            $this->buildContent($codePositionRange, $filepath),
        );
    }

    private function buildMethodSignature(Crawler $methodElement): MethodSignature
    {
        $paramTypes = [];
        foreach ($methodElement->filter('argument') as $argument) {
            $paramTypes[] = CrawlerFactory::create($argument)->filter('type')->first()->text();
        }

        uasort($paramTypes, fn(string $a, string $b) => $a <=> $b);
        $paramsOrder = array_keys($paramTypes);

        /** @var string $returnType */
        $returnType = $methodElement->attr('response');

        return MethodSignature::create($paramTypes, $paramsOrder, $returnType);
    }

    private function buildCodePositionRange(Crawler $methodElement): CodePositionRange
    {
        $startLine = (int)$methodElement->attr('startLine');
        $startColumn = (int)$methodElement->attr('startColumn');
        $endLine = (int)$methodElement->attr('endLine');
        $endColumn = (int)$methodElement->attr('endColumn');

        return CodePositionRange::create(
            CodePosition::create($startLine, $startColumn),
            CodePosition::create($endLine, $endColumn),
        );
    }

    private function buildContent(CodePositionRange $codePositionRange, string $filepath): string
    {
        return $this->filesystem->readFilePart(
            $filepath,
            Boundaries::create($codePositionRange->getStart()->getFilePos(), $codePositionRange->getEnd()->getFilePos() + 1)
        );
    }
}
