<?php

namespace App\Report\Converter;

use App\Configuration\Configuration;
use App\Model\MethodScoresMapping;
use App\Model\SourceCloneMethodScoresMapping;
use App\OutputFormatter\Model\Method\MethodSignatureOutputFormatter;
use LeoVie\PhpCleanCode\Model\Score;

class SourceCloneMethodScoreMappingsToArrayConverter
{
    public function __construct(private MethodSignatureOutputFormatter $methodSignatureOutput)
    {
    }

    /**
     * @param array<int, SourceCloneMethodScoresMapping> $sortedSourceCloneMethodScoreMappings
     *
     * @return array<int, array{sourceClone: array{methods: array<array{method: array{name: string, methodSignature: non-empty-string, filepath: string, codePositionRange: array{start: array{line: int, filePos: int}, end: array{line: int, filePos: int}, countOfLines: int}, content: string}, scores: array<array{scoreType: string, points: int}>}>}}>
     */
    public function sourceCloneMethodScoresMappingToArray(array $sortedSourceCloneMethodScoreMappings, Configuration $configuration): array
    {
        return array_map(function (SourceCloneMethodScoresMapping $sourceCloneMethodScoresMapping) use ($configuration): array {
            return [
                'sourceClone' => [
                    'methods' => array_map(function (MethodScoresMapping $methodScoresMapping) use ($configuration): array {
                        $method = $methodScoresMapping->getMethod();
                        return [
                            'method' => [
                                'name' => $method->getName(),
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

    private function convertAbsoluteFilepathToProjectRelative(string $absolute, string $projectRoot): string
    {
        /** @var string $projectRelativePath */
        $projectRelativePath = \Safe\preg_replace("@^$projectRoot@", '', $absolute);

        return $projectRelativePath;
    }
}