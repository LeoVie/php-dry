<?php

declare(strict_types=1);

namespace App\Tests\Unit\Grouper;

use App\Grouper\StringableByValueGrouper;
use PHPUnit\Framework\TestCase;
use Stringable;

class StringableByValueGrouperTest extends TestCase
{
    /** @dataProvider groupProvider */
    public function testGroup(array $expected, array $stringable): void
    {
        self::assertEquals($expected, (new StringableByValueGrouper())->group($stringable));
    }

    public function groupProvider(): array
    {
        return [
            'no duplicates' => [
                'expected' => [
                    [$this->createStringable('abc')],
                    [$this->createStringable('def')],
                    [$this->createStringable('ghi')],
                ],
                'stringable' => [
                    $this->createStringable('abc'),
                    $this->createStringable('ghi'),
                    $this->createStringable('def'),
                ],
            ],
            'only duplicates' => [
                'expected' => [
                    [
                        $this->createStringable('abc'),
                        $this->createStringable('abc'),
                        $this->createStringable('abc'),
                    ],
                ],
                'stringable' => [
                    $this->createStringable('abc'),
                    $this->createStringable('abc'),
                    $this->createStringable('abc'),
                ],
            ],
            'mixed' => [
                'expected' => [
                    [
                        $this->createStringable('abc'),
                        $this->createStringable('abc'),
                    ],
                    [
                        $this->createStringable('def'),
                        $this->createStringable('def'),
                    ],
                    [
                        $this->createStringable('ghi'),
                    ],
                ],
                'stringable' => [
                    $this->createStringable('abc'),
                    $this->createStringable('ghi'),
                    $this->createStringable('def'),
                    $this->createStringable('abc'),
                    $this->createStringable('def'),
                ],
            ],
        ];
    }

    private function createStringable(string $string): Stringable
    {
        $stringable = $this->createMock(Stringable::class);
        $stringable->method('__toString')->willReturn($string);

        return $stringable;
    }
}