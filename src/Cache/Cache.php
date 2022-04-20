<?php

namespace App\Cache;

use App\Configuration\Configuration;
use LeoVie\PhpFilesystem\Service\Filesystem;

class Cache
{
    private array $cache = [];
    private bool $wasPopulated = false;
    private string $cacheFilepath;

    public function __construct(private Filesystem $filesystem)
    {
    }

    public function setCacheFilepath(string $cacheFile): void
    {
        $this->cacheFilepath = Configuration::instance()->getCachePath() . $cacheFile;
    }

    private function populate(): void
    {
        if ($this->filesystem->fileExists($this->cacheFilepath)) {
            $serializedCache = $this->filesystem->readFile($this->cacheFilepath);

            $this->cache = unserialize($serializedCache);
        }

        $this->wasPopulated = true;
    }

    public function store(string $key, mixed $value): void
    {
        $this->cache[$key] = $value;

        $this->filesystem->writeFile($this->cacheFilepath, serialize($this->cache));
    }

    public function get(string $key): mixed
    {
        if ($this->wasPopulated) {
            $this->populate();
        }

        if (!array_key_exists($key, $this->cache)) {
            return null;
        }

        return $this->cache[$key];
    }
}