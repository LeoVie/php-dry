<?php

namespace App\Output;

use App\Configuration\Configuration;
use App\Model\Method\Method;
use App\Model\MethodScoresMapping;
use App\Model\SourceClone\SourceClone;
use App\Model\SourceCloneMethodScoresMapping;
use App\OutputFormatter\Model\Method\MethodSignatureOutputFormatter;
use App\ServiceFactory\EnvironmentFactory;
use App\ServiceFactory\FileSystemLoaderFactory;
use LeoVie\PhpCleanCode\Model\Score;
use Safe\Exceptions\FilesystemException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HtmlOutput
{
    public function __construct(
        private MethodSignatureOutputFormatter $methodSignatureOutput,
        private FileSystemLoaderFactory $fileSystemLoaderFactory,
        private EnvironmentFactory $environmentFactory,
    )
    {
    }

    /**
     * @param SourceCloneMethodScoresMapping[] $sourceCloneMethodScoresMappings
     *
     * @throws FilesystemException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function createReport(array $sourceCloneMethodScoresMappings, Configuration $configuration): void
    {
        if (!$configuration->getReportConfiguration()->getHtml()) {
            return;
        }

        $loader = $this->fileSystemLoaderFactory->create(__DIR__ . '/../../templates/php-dry');
        $twig = $this->environmentFactory->create($loader, ['cache' => '/tmp/twig_compilation_cache']);

        $template = $twig->load('php-dry.html.twig');

        $sortedSourceCloneMethodScoreMappings = [
            SourceClone::TYPE_1 => [],
            SourceClone::TYPE_2 => [],
            SourceClone::TYPE_3 => [],
            SourceClone::TYPE_4 => [],
        ];

        foreach ($sourceCloneMethodScoresMappings as $sourceCloneMethodScoresMapping) {
            $methodScoresMappings = $sourceCloneMethodScoresMapping->getMethodScoresMappings();

            $methodScoresMappingsWithRelativePath = [];
            foreach ($methodScoresMappings as $methodScoresMapping) {
                $methodScoresMappingsWithRelativePath[] = MethodScoresMapping::create(
                    $this->convertMethodFilepathToProjectRelative($methodScoresMapping->getMethod(), $configuration->getDirectory()),
                    $methodScoresMapping->getScores()
                );
            }

            $sourceCloneMethodScoresMappingWithRelativePath = SourceCloneMethodScoresMapping::create(
                $sourceCloneMethodScoresMapping->getSourceClone(),
                $methodScoresMappingsWithRelativePath
            );

            $cloneType = $sourceCloneMethodScoresMappingWithRelativePath->getSourceClone()->getType();
            $sortedSourceCloneMethodScoreMappings[$cloneType][] = $sourceCloneMethodScoresMappingWithRelativePath;
        }

        $output = [
            SourceClone::TYPE_1 => $this->sourceCloneMethodScoresMappingToArray($sortedSourceCloneMethodScoreMappings[SourceClone::TYPE_1], $configuration),
            SourceClone::TYPE_2 => $this->sourceCloneMethodScoresMappingToArray($sortedSourceCloneMethodScoreMappings[SourceClone::TYPE_2], $configuration),
            SourceClone::TYPE_3 => $this->sourceCloneMethodScoresMappingToArray($sortedSourceCloneMethodScoreMappings[SourceClone::TYPE_3], $configuration),
            SourceClone::TYPE_4 => $this->sourceCloneMethodScoresMappingToArray($sortedSourceCloneMethodScoreMappings[SourceClone::TYPE_4], $configuration),
        ];

        $htmlDirectory = rtrim($configuration->getReportConfiguration()->getHtml()->getDirectory()) . '/php-dry_html-report/';
        $htmlFilepath = $htmlDirectory . 'php-dry.html';

        if (!file_exists($htmlDirectory)) {
            \Safe\mkdir($htmlDirectory);
        }
        if (!file_exists($htmlDirectory . '/resources/')) {
            \Safe\mkdir($htmlDirectory . '/resources/');
        }
        if (!file_exists($htmlDirectory . '/resources/icons/')) {
            \Safe\mkdir($htmlDirectory . '/resources/icons/');
        }

        \Safe\copy(__DIR__ . '/../../resources/icons/php-dry.svg', $htmlDirectory . '/resources/icons/php-dry.svg');

        \Safe\file_put_contents($htmlFilepath, $template->render([
            'output' => $output,
        ]));
    }

    private function convertMethodFilepathToProjectRelative(Method $method, string $projectRoot): Method
    {
        return Method::create(
            $method->getMethodSignature(),
            $method->getName(),
            $this->convertAbsoluteFilepathToProjectRelative($method->getFilepath(), $projectRoot),
            $method->getCodePositionRange(),
            $method->getContent(),
        );
    }

    private function convertAbsoluteFilepathToProjectRelative(string $absolute, string $projectRoot): string
    {
        /** @var string $projectRelativePath */
        $projectRelativePath = \Safe\preg_replace("@^$projectRoot@", '', $absolute);

        return $projectRelativePath;
    }

    /**
     * @param array<int, SourceCloneMethodScoresMapping> $sortedSourceCloneMethodScoreMappings
     *
     * @return array<mixed>
     */
    private function sourceCloneMethodScoresMappingToArray(array $sortedSourceCloneMethodScoreMappings, Configuration $configuration): array
    {
        return array_map(function (SourceCloneMethodScoresMapping $sourceCloneMethodScoresMapping) use ($configuration): array {
            return [
                'sourceClone' => [
                    'methods' => array_map(function (MethodScoresMapping $methodScoresMapping) use ($configuration): array {
                        $method = $methodScoresMapping->getMethod();
                        return [
                            'method' => [
                                'methodSignature' => 'function ' . $method->getName() . $this->methodSignatureOutput->format($method->getMethodSignature()),
                                'filepath' => $this->convertAbsoluteFilepathToProjectRelative($method->getFilepath(), $configuration->getDirectory()),
                                'codePositionRange' => [
                                    'start' => [
                                        'line' => $method->getCodePositionRange()->getStart()->getLine(),
                                        'filePos' => $method->getCodePositionRange()->getStart()->getFilePos(),
                                    ],
                                    'end' => [
                                        'line' => $method->getCodePositionRange()->getEnd()->getLine(),
                                        'filePos' => $method->getCodePositionRange()->getEnd()->getFilePos(),
                                    ],
                                    'countOfLines' => $method->getCodePositionRange()->countOfLines(),
                                ],
                                'content' => $method->getContent(),
                            ],
                            'scores' => array_map(function (Score $score): array {
                                return [
                                    'scoreType' => $score->getScoreType(),
                                    'points' => $score->getPoints(),
                                ];
                            }, $methodScoresMapping->getScores()),
                        ];
                    }, $sourceCloneMethodScoresMapping->getMethodScoresMappings()),
                ],
            ];
        }, $sortedSourceCloneMethodScoreMappings);
    }
}
