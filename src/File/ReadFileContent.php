<?php

declare(strict_types=1);

namespace App\File;

use App\Exception\InvalidPartBoundaries;
use App\Service\FileSystem;
use Safe\Exceptions\FilesystemException;

class ReadFileContent
{
    public function __construct(private FileSystem $fileSystem)
    {
    }

    /**
     * @throws FilesystemException
     */
    public function readPart(string $filepath, int $startPos, int $endPos): string
    {
        if ($startPos > $endPos) {
            throw InvalidPartBoundaries::create($startPos, $endPos);
        }

        return substr($this->fileSystem->readFile($filepath), $startPos, $endPos - $startPos);
    }
}