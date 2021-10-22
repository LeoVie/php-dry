<?php

declare(strict_types=1);

namespace App\Tests\Unit\Find;

use App\Exception\InvalidBoundaries;
use App\File\ReadFileContent;
use LeoVie\PhpFilesystem\Service\Filesystem;
use PHPUnit\Framework\TestCase;

class ReadFileContentTest extends TestCase
{
    /** @dataProvider readPartProvider */
    public function testReadPart(string $expected, string $fileContent, int $startPos, int $endPos): void
    {
        $fileSystem = $this->createMock(Filesystem::class);
        $fileSystem->method('readFile')->willReturn($fileContent);

        self::assertSame($expected, (new ReadFileContent($fileSystem))->readPart('', $startPos, $endPos));
    }

    public function readPartProvider(): array
    {
        return [
            'empty file' => [
                'expected' => '',
                'fileContent' => '',
                'startPos' => 5,
                'endPos' => 60,
            ],
            'startPos = endPos' => [
                'expected' => '',
                'fileContent' => 'this is the file content',
                'startPos' => 5,
                'endPos' => 5,
            ],
            'endPos > startPos' => [
                'expected' => 'is th',
                'fileContent' => 'this is the file content',
                'startPos' => 5,
                'endPos' => 10,
            ],
        ];
    }

    public function testReadPartThrows(): void
    {
        $fileSystem = $this->createMock(Filesystem::class);

        self::expectException(InvalidBoundaries::class);
        (new ReadFileContent($fileSystem))->readPart('', 10, 5);
    }
}