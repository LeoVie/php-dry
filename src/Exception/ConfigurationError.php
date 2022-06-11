<?php

declare(strict_types=1);

namespace App\Exception;

class ConfigurationError extends \Exception
{
    private function __construct(string $configKey)
    {
        parent::__construct(sprintf('Configuration key "%s" was not found and no default value exist', $configKey));
    }

    public static function create(string $configKey): self
    {
        return new self($configKey);
    }
}