<?php

namespace App\Report;

use App\Model\SourceClone\SourceClone;

class Report
{
    /**
     * @param array<int, array{sourceClone: array{methods: array<array{method: array{name: string, methodSignature: non-empty-string, filepath: string, codePositionRange: array{start: array{line: int, filePos: int}, end: array{line: int, filePos: int}, countOfLines: int}, content: string}}>}}> $type1
     * @param array<int, array{sourceClone: array{methods: array<array{method: array{name: string, methodSignature: non-empty-string, filepath: string, codePositionRange: array{start: array{line: int, filePos: int}, end: array{line: int, filePos: int}, countOfLines: int}, content: string}}>}}> $type2
     * @param array<int, array{sourceClone: array{methods: array<array{method: array{name: string, methodSignature: non-empty-string, filepath: string, codePositionRange: array{start: array{line: int, filePos: int}, end: array{line: int, filePos: int}, countOfLines: int}, content: string}}>}}> $type3
     * @param array<int, array{sourceClone: array{methods: array<array{method: array{name: string, methodSignature: non-empty-string, filepath: string, codePositionRange: array{start: array{line: int, filePos: int}, end: array{line: int, filePos: int}, countOfLines: int}, content: string}}>}}> $type4
     */
    private function __construct(
        private array $type1,
        private array $type2,
        private array $type3,
        private array $type4
    )
    {
    }

    /**
     * @param array<int, array{sourceClone: array{methods: array<array{method: array{name: string, methodSignature: non-empty-string, filepath: string, codePositionRange: array{start: array{line: int, filePos: int}, end: array{line: int, filePos: int}, countOfLines: int}, content: string}}>}}> $type1
     * @param array<int, array{sourceClone: array{methods: array<array{method: array{name: string, methodSignature: non-empty-string, filepath: string, codePositionRange: array{start: array{line: int, filePos: int}, end: array{line: int, filePos: int}, countOfLines: int}, content: string}}>}}> $type2
     * @param array<int, array{sourceClone: array{methods: array<array{method: array{name: string, methodSignature: non-empty-string, filepath: string, codePositionRange: array{start: array{line: int, filePos: int}, end: array{line: int, filePos: int}, countOfLines: int}, content: string}}>}}> $type3
     * @param array<int, array{sourceClone: array{methods: array<array{method: array{name: string, methodSignature: non-empty-string, filepath: string, codePositionRange: array{start: array{line: int, filePos: int}, end: array{line: int, filePos: int}, countOfLines: int}, content: string}}>}}> $type4
     */
    public static function create(array $type1, array $type2, array $type3, array $type4): self
    {
        return new self($type1, $type2, $type3, $type4);
    }

    /** @return array<string, array<int, array{sourceClone: array{methods: array<array{method: array{name: string, methodSignature: non-empty-string, filepath: string, codePositionRange: array{start: array{line: int, filePos: int}, end: array{line: int, filePos: int}, countOfLines: int}, content: string}}>}}>> */
    public function getAll(): array
    {
        return [
            SourceClone::TYPE_1 => $this->type1,
            SourceClone::TYPE_2 => $this->type2,
            SourceClone::TYPE_3 => $this->type3,
            SourceClone::TYPE_4 => $this->type4,
        ];
    }
}