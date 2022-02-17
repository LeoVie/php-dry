<?php

declare(strict_types=1);

namespace App\Model\CodePosition;

use JsonSerializable;

class CodePosition implements JsonSerializable
{
    public static function create(int $line, int $filePos): self
    {
        return new self($line, $filePos);
    }

    private function __construct(private int $line, private int $filePos)
    {
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getFilePos(): int
    {
        return $this->filePos;
    }

    /** @return array{'line': int, 'filePos': int} */
    public function jsonSerialize(): array
    {
        return [
            'line' => $this->getLine(),
            'filePos' => $this->getFilePos(),
        ];
    }
}