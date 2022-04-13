<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use Twig\Loader\FilesystemLoader;

final class FileSystemLoaderFactory
{
    /** @param string|array<int, string> $paths */
    public function create(string|array $paths = [], string|null $rootPath = null): FilesystemLoader
    {
        return new FilesystemLoader($paths, $rootPath);
    }
}
