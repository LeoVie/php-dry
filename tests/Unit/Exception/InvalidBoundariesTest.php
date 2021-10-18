<?php

declare(strict_types=1);

namespace App\Tests\Unit\Exception;

use App\Exception\InvalidBoundaries;
use PHPUnit\Framework\TestCase;

class InvalidBoundariesTest extends TestCase
{
    /** @dataProvider createProvider */
    public function testCreate(string $expected, int $start, int $end): void
    {
        self::assertSame($expected, InvalidBoundaries::create($start, $end)->getMessage());
    }

    public function createProvider(): array
    {
        return [
            [
                'expected' => 'Start boundary 100 is greater than end boundary 0.',
                'start' => 100,
                'end' => 0,
            ],
            [
                'expected' => 'Start boundary 99 is greater than end boundary 98.',
                'start' => 99,
                'end' => 98,
            ],
        ];
    }
}