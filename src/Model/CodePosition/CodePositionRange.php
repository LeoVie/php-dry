<?php

declare(strict_types=1);

namespace App\Model\CodePosition;

use JetBrains\PhpStorm\Internal\TentativeType;
use JsonSerializable;
use Safe\Exceptions\StringsException;
use Stringable;

class CodePositionRange implements Stringable, JsonSerializable
{
    public static function create(CodePosition $start, CodePosition $end): self
    {
        return new self($start, $end);
    }

    private function __construct(private CodePosition $start, private CodePosition $end)
    {
    }

    public function getStart(): CodePosition
    {
        return $this->start;
    }

    public function getEnd(): CodePosition
    {
        return $this->end;
    }

    public function countOfLines(): int
    {
        return $this->getEnd()->getLine() - $this->getStart()->getLine();
    }

    /** @throws StringsException */
    public function __toString(): string
    {
        return \Safe\sprintf(
            '%s - %s (%s lines)',
            $this->getStart()->__toString(),
            $this->getEnd()->__toString(),
            $this->countOfLines()
        );
    }

    /** @return array{'start': CodePosition, 'end': CodePosition, 'countOfLines': int} */
    public function jsonSerialize(): array
    {
        return [
            'start' => $this->getStart(),
            'end' => $this->getEnd(),
            'countOfLines' => $this->countOfLines(),
        ];
    }
}