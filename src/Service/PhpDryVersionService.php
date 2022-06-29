<?php

declare(strict_types=1);

namespace App\Service;

use LeoVie\PhpFilesystem\Service\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class PhpDryVersionService
{
    public function __construct(
        private KernelInterface $kernel,
        private Filesystem $filesystem,
    )
    {}

    public function getCurrentVersion(): string
    {
        $versionFilepath = $this->kernel->getProjectDir() . '/VERSION';

        return trim($this->filesystem->readFile($versionFilepath));
    }
}