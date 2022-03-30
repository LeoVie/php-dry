<?php

declare(strict_types=1);

namespace App\File;

use App\ServiceFactory\FinderFactory;
use SplFileInfo;

class FindFiles
{
    public function __construct(private FinderFactory $finderFactory)
    {
    }

    /** @return string[] */
    public function findPhpFilesInPath(string $path): array
    {
        return array_map(
            fn (SplFileInfo $f) => $f->__toString(),
            iterator_to_array($this->finderFactory->instance()->in($path)->name('*.php')->files())
        );
    }
}
