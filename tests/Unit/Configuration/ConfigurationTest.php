<?php

declare(strict_types=1);

namespace App\Tests\Unit\Configuration;

use App\Configuration\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    /** @dataProvider directoryProvider */
    public function testDirectory(string $expected, Configuration $configuration): void
    {
        self::assertSame($expected, $configuration->directory());
    }

    public function directoryProvider(): array
    {
        return [
            [
                'expected' => '/var/www/foo/',
                'configuration' => Configuration::create('/var/www/foo/', 0, 0, ''),
            ],
            [
                'expected' => '/mnt/c/bla/bla/',
                'configuration' => Configuration::create('/mnt/c/bla/bla/', 0, 0, ''),
            ],
        ];
    }

    /** @dataProvider minLinesForType1AndType2ClonesProvider */
    public function testMinSimilarTokens(int $expected, Configuration $configuration): void
    {
        self::assertSame($expected, $configuration->minSimilarTokensPercent());
    }

    public function minLinesForType1AndType2ClonesProvider(): array
    {
        return [
            [
                'expected' => 10,
                'configuration' => Configuration::create('', 10, 0, ''),
            ],
            [
                'expected' => 5,
                'configuration' => Configuration::create('', 5, 0, ''),
            ],
        ];
    }

    /** @dataProvider countOfParamSetsForType4ClonesProvider */
    public function testCountOfParamSetsForType4Clones(int $expected, Configuration $configuration): void
    {
        self::assertSame($expected, $configuration->countOfParamSetsForType4Clones());
    }

    public function countOfParamSetsForType4ClonesProvider(): array
    {
        return [
            [
                'expected' => 7,
                'configuration' => Configuration::create('', 0, 7, ''),
            ],
            [
                'expected' => 2,
                'configuration' => Configuration::create('', 0,  2, ''),
            ],
        ];
    }

    /** @dataProvider htmlReportFileProvider */
    public function testHtmlReportFile(string $expected, Configuration $configuration): void
    {
        self::assertSame($expected, $configuration->htmlReportFile());
    }

    public function htmlReportFileProvider(): array
    {
        return [
            [
                'expected' => '/var/www/report.html',
                'configuration' => Configuration::create('', 0, 0, '/var/www/report.html'),
            ],
            [
                'expected' => 'bla/foo/bar.html',
                'configuration' => Configuration::create('', 0,  0, 'bla/foo/bar.html'),
            ],
        ];
    }
}