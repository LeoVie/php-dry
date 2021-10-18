<?php

declare(strict_types=1);

namespace App\Tests\Unit\Exception;

use App\Exception\NoReplacementRegistered;
use PHPUnit\Framework\TestCase;

class NoReplacementRegisteredTest extends TestCase
{
    /** @dataProvider createProvider */
    public function testCreate(string $expectedMessage, string $original): void
    {
        self::assertSame($expectedMessage, NoReplacementRegistered::create($original)->getMessage());
    }

    public function createProvider(): array
    {
        return [
            [
                'expected' => 'Node replacement registered for $abc.',
                'original' => '$abc',
            ],
            [
                'expected' => 'Node replacement registered for $foo.',
                'original' => '$foo',
            ],
        ];
    }
}