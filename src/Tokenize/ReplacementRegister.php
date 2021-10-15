<?php

declare(strict_types=1);

namespace App\Tokenize;

use App\Exception\NoReplacementRegistered;
use Safe\Exceptions\StringsException;

class ReplacementRegister
{
    private int $lastRegisteredIndex = -1;

    /** @var array<string, string> */
    private array $registered = [];

    private function __construct(private string $replacementPrefix)
    {}

    public static function create(string $replacementPrefix): self
    {
        return new self($replacementPrefix);
    }

    public function register(string $original): self
    {
        $this->lastRegisteredIndex++;
        $this->registered[$original] = $this->replacementPrefix . $this->lastRegisteredIndex;

        return $this;
    }

    public function isReplacementRegistered(string $original): bool
    {
        return array_key_exists($original, $this->registered);
    }

    /**
     * @throws StringsException
     * @throws NoReplacementRegistered
     */
    public function getReplacement(string $original): string
    {
        if (!$this->isReplacementRegistered($original)) {
            throw NoReplacementRegistered::create($original);
        }

        return $this->registered[$original];
    }
}