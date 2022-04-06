<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Configuration\Configuration;
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
            ->willReturn(__DIR__ . '/../../testdata/phpDocumentor_structure.xml');

        $filesystem = $this->createMock(Filesystem::class);

        $methods = (new FindMethodsInPathsService($filesystem))->findAll($configuration);
    }
}
