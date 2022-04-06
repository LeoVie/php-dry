<?php

namespace App\Tests\Functional\Service;

use App\Configuration\Configuration;
use App\Service\PhpDocumentorRunner;
use PHPUnit\Framework\TestCase;

class PhpDocumentorRunnerTest extends TestCase
{
    public function testRun(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getPhpDocumentorExecutablePath')->willReturn('/home/ubuntu/projects/phpDocumentor/bin/phpdoc');
        $configuration->method('getPhpDocumentorReportPath')->willReturn(__DIR__ . '/phpDocumentorReport');
        $configuration->method('getDirectory')->willReturn(__DIR__ . '/../../testdata/clone-detection-testdata');

        (new PhpDocumentorRunner())->run($configuration);

        self::assertXmlFileEqualsXmlFile(__DIR__ . '/expected_phpDocumentor_structure.xml', __DIR__ . '/phpDocumentorReport/structure.xml');
    }
}