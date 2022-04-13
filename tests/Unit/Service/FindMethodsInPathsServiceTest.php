<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Configuration\Configuration;
use App\Model\CodePosition\CodePosition;
use App\Model\CodePosition\CodePositionRange;
use App\Model\Method\Method;
use App\Model\Method\MethodSignature;
use App\Service\FindMethodsInPathsService;
use LeoVie\PhpFilesystem\Service\Filesystem;
use PHPUnit\Framework\TestCase;

class FindMethodsInPathsServiceTest extends TestCase
{
    public function testFindAll(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $configuration
            ->method('getPhpDocumentorReportPath')
            ->willReturn(__DIR__ . '/../../testdata/phpDocumentor_structure_small.xml');

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('readFilePart')->willReturnArgument(0);

        $methods = (new FindMethodsInPathsService($filesystem))->findAll($configuration);

        $expected = [
            Method::create(
                MethodSignature::create(
                    ['array<int,int>', 'int'],
                    'array<int,int>'
                ),
                'foo',
                '01_A.php',
                CodePositionRange::create(
                    CodePosition::create(10, 143),
                    CodePosition::create(18, 324),
                ),
                '01_A.php'
            ),
            Method::create(
                MethodSignature::create(
                    ['array<int,int>', 'int'],
                    'array<int,array<int,int>|int>'
                ),
                'bar',
                '02_B.php',
                CodePositionRange::create(
                    CodePosition::create(10, 159),
                    CodePosition::create(13, 239),
                ),
                '02_B.php'
            ),
        ];

        self::assertEqualsCanonicalizing($expected, $methods);
    }
}
