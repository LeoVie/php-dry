<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use Twig\Environment;
use Twig\Loader\LoaderInterface;

final class EnvironmentFactory
{
    /** @param array<string, mixed> $options */
    public function create(LoaderInterface $loader, array $options = []): Environment
    {
        return new Environment($loader, $options);
    }
}
