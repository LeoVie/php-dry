<?php

class DTO
{
    /** @param array<int, int> $p1 */
    public function __construct(
        private array $p1,
        private int   $p2
    )
    {
    }

    public function getP1(): array
    {
        return $this->p1;
    }

    public function getP2(): int
    {
        return $this->p2;
    }
}