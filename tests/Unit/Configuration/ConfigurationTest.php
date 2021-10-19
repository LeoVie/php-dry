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
                'configuration' => Configuration::create('/var/www/foo/', 0, 0),
            ],
            [
                'expected' => '/mnt/c/bla/bla/',
                'configuration' => Configuration::create('/mnt/c/bla/bla/', 0, 0),
            ],
        ];
    }

    /** @dataProvider minLinesProvider */
    public function testMinLines(int $expected, Configuration $configuration): void
    {
        self::assertSame($expected, $configuration->minLines());
    }

    public function minLinesProvider(): array
    {
        return [
            [
                'expected' => 10,
                'configuration' => Configuration::create('', 10, 0),
            ],
            [
                'expected' => 5,
                'configuration' => Configuration::create('', 5, 0),
            ],
        ];
    }

    /** @dataProvider countOfParamSetsProvider */
    public function testCountOfParamSets(int $expected, Configuration $configuration): void
    {
        self::assertSame($expected, $configuration->countOfParamSets());
    }

    public function countOfParamSetsProvider(): array
    {
        return [
            [
                'expected' => 7,
                'configuration' => Configuration::create('', 0, 7),
            ],
            [
                'expected' => 2,
                'configuration' => Configuration::create('', 0, 2),
            ],
        ];
    }
}