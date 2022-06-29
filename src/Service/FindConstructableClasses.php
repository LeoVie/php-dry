<?php

declare(strict_types=1);

namespace App\Service;

use App\Command\Output\DetectClonesCommandOutput;
use App\Configuration\Configuration;
use App\Exception\ClassNotConstructable;
use App\Exception\PhpDocumentorFailed;
use App\Model\ClassModel\ClassModel;
use App\Model\Method\MethodSignature;
use App\ServiceFactory\CrawlerFactory;
use Safe\Exceptions\FilesystemException;
use Symfony\Component\DomCrawler\Crawler;

class FindConstructableClasses
{
    public function __construct(private PhpDocumentorRunner $phpDocumentorRunner)
    {
    }

    /**
     * @return array<class-string, ClassModel>
     *
     * @throws FilesystemException
     * @throws PhpDocumentorFailed
     */
    public function findAll(DetectClonesCommandOutput $commandOutput, string $directory): array
    {
        $this->phpDocumentorRunner->runMinimal($directory);

        $reportPath = Configuration::instance()->getPhpDocumentorReportPath();
        $reportXmlFilepath = rtrim($reportPath, '/') . '/structure.xml';

        file_put_contents(__DIR__ . '/structure.xml', file_get_contents($reportXmlFilepath));

        $crawler = CrawlerFactory::create(\Safe\file_get_contents($reportXmlFilepath));

        return $this->findClassesInProject($commandOutput, $crawler->filter('project')->first());
    }

    /** @return array<class-string, ClassModel> */
    private function findClassesInProject(DetectClonesCommandOutput $commandOutput, Crawler $projectElement): array
    {
        $fileElements = [];
        foreach ($projectElement->children() as $element) {
            if ($element->nodeName === 'file') {
                $fileElements[] = $element;
            }
        }

        $fileElements = $commandOutput->createProgressBarIterator($fileElements);

        $classes = [];
        foreach ($fileElements as $fileElement) {
            $classes = array_merge($classes, $this->findClassesInFile(CrawlerFactory::create($fileElement)));
        }

        return $classes;
    }

    /** @return array<class-string, ClassModel> */
    private function findClassesInFile(Crawler $fileElement): array
    {
        $classes = [];
        foreach ($fileElement->children() as $element) {
            if ($element->nodeName === 'class') {
                try {
                    $class = $this->buildClass(CrawlerFactory::create($element));
                    $classes[$class->getFQN()] = $class;
                } catch (ClassNotConstructable $e) {
                    continue;
                }
            }
        }

        return $classes;
    }

    /** @throws ClassNotConstructable */
    private function buildClass(Crawler $classElement): ClassModel
    {
        /** @var class-string $classFQN */
        $classFQN = '';

        foreach ($classElement->children() as $element) {
            if ($element->nodeName === 'full_name') {
                /** @var class-string $classFQN */
                $classFQN = $element->nodeValue;

                break;
            }
        }

        $constructorMethod = $this->findConstructorInClass($classElement, $classFQN);

        if ($constructorMethod === null) {
            return ClassModel::create($classFQN, MethodSignature::create([], [], $classFQN));
        }


        return ClassModel::create(
            $classFQN,
            $this->buildConstructorSignature($constructorMethod, $classFQN)
        );
    }

    /** @throws ClassNotConstructable */
    private function findConstructorInClass(Crawler $classElement, string $classFQN): ?Crawler
    {
        foreach ($classElement->children() as $element) {
            if ($element->nodeName !== 'method') {
                continue;
            }

            $methodElement = CrawlerFactory::create($element);

            foreach ($methodElement->children() as $methodElementChildren) {
                if ($methodElementChildren->nodeName !== 'name') {
                    continue;
                }

                if ($methodElementChildren->nodeValue !== '__construct') {
                    continue;
                }

                if ($methodElement->attr('visibility') !== 'public') {
                    throw ClassNotConstructable::create($classFQN);
                }

                return $methodElement;
            }
        }

        return null;
    }

    private function buildConstructorSignature(Crawler $constructorMethodElement, string $classFQN): MethodSignature
    {
        $paramTypes = [];

        foreach ($constructorMethodElement->filter('argument') as $argument) {
            $paramTypes[] = CrawlerFactory::create($argument)->filter('type')->first()->text();
        }

        uasort($paramTypes, fn(string $a, string $b) => $a <=> $b);
        $paramsOrder = array_keys($paramTypes);

        return MethodSignature::create($paramTypes, $paramsOrder, $classFQN);
    }
}
