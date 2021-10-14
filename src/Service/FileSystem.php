<?php

declare(strict_types=1);

namespace App\Service;

use Safe\Exceptions\FilesystemException;

class FileSystem
{
    /** @throws FilesystemException */
    public function readFile(string $filepath): string
    {
        return \Safe\file_get_contents($filepath);
    }
}