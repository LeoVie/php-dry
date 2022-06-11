<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Configuration\Configuration;
use App\Model\CodePosition\CodePosition;
use App\Model\CodePosition\CodePositionRange;
use App\Model\Method\Method;
use App\Model\Method\MethodSignature;
use App\Service\FindMethodsInPathsService;
use App\Service\PhpDocumentorRunner;
use LeoVie\PhpFilesystem\Service\Filesystem;
use PHPUnit\Framework\TestCase;

class FindMethodsInPathsServiceTest extends TestCase
{
    public function testFindAll(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $configuration
            ->method('getPhpDocumentorReportPath')
            ->willReturn(__DIR__ . '/../../testdata/phpDocumentor/small_report');
        Configuration::setInstance($configuration);

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('readFilePart')->willReturnArgument(0);

        $phpDocumentorRunner = $this->createMock(PhpDocumentorRunner::class);
        $phpDocumentorRunner->method('run');

        $projectDirectory = '/var/www/';
        $methods = (new FindMethodsInPathsService($filesystem, $phpDocumentorRunner))->findAll($projectDirectory);

        $expected = [
            Method::create(
                MethodSignature::create(
                    ['array<int,int>', 'int'],
                    [0, 1],
                    'array<int,int>'
                ),
                'foo',
                '/var/www/01_A.php',
                CodePositionRange::create(
                    CodePosition::create(10, 143),
                    CodePosition::create(18, 324),
                ),
                '/var/www/01_A.php',
                $projectDirectory
            ),
            Method::create(
                MethodSignature::create(
                    ['array<int,int>', 'int'],
                    [0, 1],
                    'array<int,array<int,int>|int>'
                ),
                'bar',
                '/var/www/02_B.php',
                CodePositionRange::create(
                    CodePosition::create(10, 159),
                    CodePosition::create(13, 239),
                ),
                '/var/www/02_B.php',
                $projectDirectory
            ),
        ];

        self::assertEqualsCanonicalizing($expected, $methods);
    }
}
