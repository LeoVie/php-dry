<?php

declare(strict_types=1);

namespace App\Tests\Unit\File;

use App\File\FindFiles;
use App\ServiceFactory\FinderFactory;
use PHPUnit\Framework\TestCase;

class FindFilesTest extends TestCase
{
    /** @dataProvider findPhpFilesInPathProvider */
    public function testFindPhpFilesInPath(string $path, array $expected): void
    {
        self::assertEqualsCanonicalizing($expected, (new FindFiles(new FinderFactory()))->findPhpFilesInPath($path));
    }

    public function findPhpFilesInPathProvider(): array
    {
        $testdataDir = \Safe\realpath(__DIR__ . '/../../testdata');

        return [
            'set1' => [
                $testdataDir . '/set1/',
                [
                    $testdataDir . '/set1/after-rector/for-loop.php',
                    $testdataDir . '/set1/after-rector/foreach-loop.php',
                    $testdataDir . '/set1/before-rector/for-loop.php',
                    $testdataDir . '/set1/before-rector/foreach-loop.php',
                ],
            ],
            'set2' => [
                $testdataDir . '/set2/',
                [
                    $testdataDir . '/set2/AlsoOtherMethodSignature.php',
                    $testdataDir . '/set2/ExactSameAsWithFor.php',
                    $testdataDir . '/set2/OtherFunction.php',
                    $testdataDir . '/set2/OtherMethodSignature.php',
                    $testdataDir . '/set2/WithArrayMap.php',
                    $testdataDir . '/set2/WithFor.php',
                    $testdataDir . '/set2/WithForeach.php',
                    $testdataDir . '/set2/WithForWithAdditionalWhitespaceAndComments.php',
                ],
            ],
            'no php files' => [
                $testdataDir . '/file/',
                [
                ],
            ],
        ];
    }
}