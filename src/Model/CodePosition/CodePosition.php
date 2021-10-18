<?php

declare(strict_types=1);

namespace App\Model\CodePosition;

use Safe\Exceptions\StringsException;
use Stringable;

class CodePosition implements Stringable
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

    /** @throws StringsException */
    public function __toString(): string
    {
        return \Safe\sprintf('%s (position %s)', $this->getLine(), $this->getFilePos());
    }
}