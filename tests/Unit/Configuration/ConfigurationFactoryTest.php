<?php

namespace App\Tests\Unit\Configuration;

use App\Configuration\Configuration;
use App\Configuration\ConfigurationFactory;
use App\Configuration\ReportConfiguration;
use PHPUnit\Framework\TestCase;

class ConfigurationFactoryTest extends TestCase
{
    /** @dataProvider createConfigurationFromXmlProvider */
    public function testCreateConfigurationFromXml(Configuration $expected, string $xmlFilepath): void
    {
        self::assertEquals($expected, (new ConfigurationFactory())->createConfigurationFromXmlFile($xmlFilepath));
    }

    public function createConfigurationFromXmlProvider(): \Generator
    {
        yield 'php-dry-01.xml' => [
            'expected' => Configuration::create(
                false,
                50,
                80,
                false,
                10,
                false,
                ReportConfiguration::create(
                    ReportConfiguration\Cli::create(),
                    ReportConfiguration\Html::create(__DIR__ . '/../../testdata/reports/php-dry.html'),
                    ReportConfiguration\Json::create(__DIR__ . '/../../testdata/reports/php-dry.json'),
                )
            ),
            'xmlFilepath' => __DIR__ . '/../../testdata/php-dry-01.xml'
        ];

        yield 'php-dry-02.xml' => [
            'expected' => Configuration::create(
                true,
                60,
                75,
                true,
                15,
                true,
                ReportConfiguration::create(
                    ReportConfiguration\Cli::create(),
                    null,
                    ReportConfiguration\Json::create(__DIR__ . '/../../testdata/foo/bar/report.json'),
                )
            ),
            'xmlFilepath' => __DIR__ . '/../../testdata/php-dry-02.xml'
        ];
    }
}