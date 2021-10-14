<?php

declare(strict_types=1);

namespace App\File;

use App\Service\FinderService;
use SplFileInfo;

class FindFiles
{
    public function __construct(private FinderService $finderService)
    {
    }

    /** @return string[] */
    public function findPhpFilesInPath(string $path): array
    {
        return array_map(
            fn(SplFileInfo $f) => $f->__toString(),
            iterator_to_array($this->finderService->instance()->in($path)->name('*.php')->files())
        );
    }
}