<?php

namespace App\Report\Converter;

use App\Configuration\Configuration;
use App\Model\Method\Method;
use App\Model\SourceClone\SourceClone;
use App\OutputFormatter\Model\Method\MethodSignatureOutputFormatter;

class SourceClonesToArrayConverter
{
    public function __construct(private MethodSignatureOutputFormatter $methodSignatureOutput)
    {
    }

    /**
     * @param array<int, SourceClone> $clones
     *
     * @return array<int, array{sourceClone: array{methods: array<array{method: array{name: string, methodSignature: non-empty-string, filepath: string, codePositionRange: array{start: array{line: int, filePos: int}, end: array{line: int, filePos: int}, countOfLines: int}, content: string}}>}}>
     */
    public function sourceClonesToArray(array $clones): array
    {
        return array_map(function (SourceClone $clone): array {
            return [
                'sourceClone' => [
                    'methods' => array_map(function (Method $method): array {
                        return [
                            'method' => [
                                'name' => $method->getName(),
                                'methodSignature' => 'function ' . $method->getName() . $this->methodSignatureOutput->format($method->getMethodSignature()),
                                'filepath' => \Safe\realpath($method->getFilepath()),
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
                        ];
                    }, $clone->getMethodsCollection()->getAll()),
                ],
            ];
        }, $clones);
    }
}