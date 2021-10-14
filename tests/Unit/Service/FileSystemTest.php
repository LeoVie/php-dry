<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\FileSystem;
use PHPUnit\Framework\TestCase;

class FileSystemTest extends TestCase
{
    public function testReadFile(): void
    {
        self::assertSame('file', (new FileSystem())->readFile(__DIR__ . '/../../testdata/file/file.txt'));
    }
}