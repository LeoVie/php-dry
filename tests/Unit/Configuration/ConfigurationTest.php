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
                'configuration' => $this->createConfiguration(['directory' => '/var/www/foo/']),
            ],
            [
                'expected' => '/mnt/c/bla/bla/',
                'configuration' => $this->createConfiguration(['directory' => '/mnt/c/bla/bla/']),
            ],
        ];
    }

    private function createConfiguration(array $items): Configuration
    {
        return Configuration::create(
            $items['directory'] ?? '',
            $items['minSimilarTokensPercent'] ?? 0,
            $items['countOfParamSetsForType4Clones'] ?? 0,
            $items['htmlReportFile'] ?? '',
            $items['minTokenLength'] ?? 0,
            $items['enableConstructNormalization'] ?? true,
            $items['enableLCSAlgorithm'] ?? true,
        );
    }

    /** @dataProvider minSimilarTokensPercentProvider */
    public function testMinSimilarTokens(int $expected, Configuration $configuration): void
    {
        self::assertSame($expected, $configuration->minSimilarTokensPercent());
    }

    public function minSimilarTokensPercentProvider(): array
    {
        return [
            [
                'expected' => 10,
                'configuration' => $this->createConfiguration(['minSimilarTokensPercent' => 10]),
            ],
            [
                'expected' => 5,
                'configuration' => $this->createConfiguration(['minSimilarTokensPercent' => 5]),
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
                'configuration' => $this->createConfiguration(['countOfParamSetsForType4Clones' => 7]),
            ],
            [
                'expected' => 2,
                'configuration' => $this->createConfiguration(['countOfParamSetsForType4Clones' => 2]),
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
                'configuration' => $this->createConfiguration(['htmlReportFile' => '/var/www/report.html']),
            ],
            [
                'expected' => 'bla/foo/bar.html',
                'configuration' => $this->createConfiguration(['htmlReportFile' => 'bla/foo/bar.html']),
            ],
        ];
    }
}