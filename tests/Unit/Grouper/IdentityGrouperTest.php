<?php

declare(strict_types=1);

namespace App\Tests\Unit\Grouper;

use App\Grouper\IdentityGrouper;
use App\Sort\Identity;
use App\Sort\IdentitySorter;
use PHPUnit\Framework\TestCase;

class IdentityGrouperTest extends TestCase
{
    /** @dataProvider groupProvider */
    public function testGroup(array $expected, array $identities): void
    {
        $identitySorter = $this->createMock(IdentitySorter::class);
        $identitySorter->method('sort')->willReturnArgument(0);

        self::assertSame($expected, (new IdentityGrouper($identitySorter))->group($identities));
    }

    public function groupProvider(): \Generator
    {
        yield 'empty' => [
            'expected' => [],
            'identities' => [],
        ];

        $identity = $this->mockIdentity('abc');
        yield 'only one identity' => [
            'expected' => [
                [
                    $identity
                ]
            ],
            'identities' => [$identity],
        ];

        $identities = [
            $this->mockIdentity('abc'),
            $this->mockIdentity('abc'),
        ];
        yield 'same identities' => [
            'expected' => [
                [
                    $identities[0],
                    $identities[1]
                ]
            ],
            'identities' => $identities,
        ];

        $identities = [
            $this->mockIdentity('abc'),
            $this->mockIdentity('def'),
        ];
        yield 'different identities' => [
            'expected' => [
                [
                    $identities[0],
                ],
                [
                    $identities[1]
                ]
            ],
            'identities' => $identities,
        ];

        $identities = [
            $this->mockIdentity('abc'),
            $this->mockIdentity('abc'),
            $this->mockIdentity('def'),
        ];
        yield 'mixed' => [
            'expected' => [
                [
                    $identities[0],
                    $identities[1],
                ],
                [
                    $identities[2]
                ]
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