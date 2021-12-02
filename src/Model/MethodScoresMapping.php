<?php

namespace App\Model;

use App\Model\Method\Method;
use LeoVie\PhpCleanCode\Model\Score;

class MethodScoresMapping
{
    /** @param Score[] $scores */
    private function __construct(
        private Method $method,
        private array  $scores
    )
    {
    }

    /** @param Score[] $scores */
    public static function create(Method $method, array $scores): self
    {
        return new self($method, $scores);
    }

    public function getMethod(): Method
    {
        return $this->method;
    }

    /** @return Score[] */
    public function getScores(): array
    {
        return $this->scores;
    }
}