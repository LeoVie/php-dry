<?php

namespace App\Tests\Functional\Service;

use App\Configuration\Configuration;
use App\Service\PhpDocumentorRunner;
use PHPUnit\Framework\TestCase;

class PhpDocumentorRunnerTest extends TestCase
{
    private const GENERATED_STRUCTURE_XML = __DIR__ . '/phpDocumentorReport/structure.xml';
    private const TESTDATA_DIR = __DIR__ . '/../../testdata/clone-detection-testdata-with-native-types/src';

    protected function setUp(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getPhpDocumentorExecutablePath')->willReturn('tools/phpDocumentor.phar');
        $configuration->method('getPhpDocumentorReportPath')->willReturn(__DIR__ . '/phpDocumentorReport');

        Configuration::setInstance($configuration);
    }

    public function testRun(): void
    {
        (new PhpDocumentorRunner())->run(self::TESTDATA_DIR);

        self::assertXmlFileEqualsXmlFile(__DIR__ . '/expected_phpDocumentor_structure.xml', self::GENERATED_STRUCTURE_XML);
    }

    public function testRunMinimal(): void
    {
        (new PhpDocumentorRunner())->runMinimal(self::TESTDATA_DIR);

        self::assertXmlFileEqualsXmlFile(__DIR__ . '/expected_phpDocumentor_structure_minimal.xml', self::GENERATED_STRUCTURE_XML);
    }
}