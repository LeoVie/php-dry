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
                'configuration' => Configuration::create('/var/www/foo/', 0, 0, 0),
            ],
            [
                'expected' => '/mnt/c/bla/bla/',
                'configuration' => Configuration::create('/mnt/c/bla/bla/', 0, 0, 0),
            ],
        ];
    }

    /** @dataProvider minLinesForType1AndType2ClonesProvider */
    public function testminLinesForType1AndType2Clones(int $expected, Configuration $configuration): void
    {
        self::assertSame($expected, $configuration->minLinesForType1AndType2Clones());
    }

    public function minLinesForType1AndType2ClonesProvider(): array
    {
        return [
            [
                'expected' => 10,
                'configuration' => Configuration::create('', 10, 0, 0),
            ],
            [
                'expected' => 5,
                'configuration' => Configuration::create('', 5, 0, 0),
            ],
        ];
    }

    /** @dataProvider minSimilarTokensForType3ClonesProvider */
    public function testMinSimilarTokensForType3ClonesProvider(int $expected, Configuration $configuration): void
    {
        self::assertSame($expected, $configuration->minSimilarTokensForType3Clones());
    }

    public function minSimilarTokensForType3ClonesProvider(): array
    {
        return [
            [
                'expected' => 10,
                'configuration' => Configuration::create('', 0, 10, 0),
            ],
            [
                'expected' => 4,
                'configuration' => Configuration::create('', 0, 4, 0),
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
                'configuration' => Configuration::create('', 0, 0, 7),
            ],
            [
                'expected' => 2,
                'configuration' => Configuration::create('', 0, 0, 2),
            ],
        ];
    }
}