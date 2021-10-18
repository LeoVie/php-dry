<?php

declare(strict_types=1);

namespace App\Tests\Unit\Sort;

use App\Sort\Identity;
use App\Sort\IdentitySorter;
use PHPUnit\Framework\TestCase;

class IdentitySorterTest extends TestCase
{
    /** @dataProvider sortProvider */
    public function testSort(array $expected, array $identities): void
    {
        self::assertSame($expected, (new IdentitySorter())->sort($identities));
    }

    public function sortProvider(): \Generator
    {
        yield 'empty' => [
            'expected' => [],
            'identities' => [],
        ];

        $identity = $this->mockIdentity('abc');
        yield 'only one identity' => [
            'expected' => [$identity],
            'identities' => [$identity],
        ];

        $identities = [
            $this->mockIdentity('abc'),
            $this->mockIdentity('def'),
        ];
        yield 'two already sorted identities' => [
            'expected' => $identities,
            'identities' => $identities,
        ];

        $identities = [
            $this->mockIdentity('def'),
            $this->mockIdentity('abc'),
        ];
        yield 'two unsorted identities' => [
            'expected' => [$identities[1], $identities[0]],
            'identities' => $identities,
        ];

        $identities = [
            $this->mockIdentity('def'),
            $this->mockIdentity('abc'),
            $this->mockIdentity('yxz'),
            $this->mockIdentity('hij'),
            $this->mockIdentity('qrs'),
            $this->mockIdentity('abd'),
        ];
        yield 'multiple unsorted identities' => [
            'expected' => [
                $identities[1],
                $identities[5],
                $identities[0],
                $identities[3],
                $identities[4],
                $identities[2],
            ],
            'identities' => $identities,
        ];
    }

    private function mockIdentity(string $identityString): Identity
    {
        $identity = $this->createMock(Identity::class);
        $identity->method('identity')->willReturn($identityString);

        return $identity;
    }
}