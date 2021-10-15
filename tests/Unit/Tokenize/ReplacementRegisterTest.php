<?php

declare(strict_types=1);

namespace App\Tests\Unit\Tokenize;

use App\Exception\NoReplacementRegistered;
use App\Tokenize\ReplacementRegister;
use PHPUnit\Framework\TestCase;

class ReplacementRegisterTest extends TestCase
{
    /** @dataProvider isReplacementRegisteredProvider */
    public function testIsReplacementRegistered(bool $expected, string $original, ReplacementRegister $replacementRegister): void
    {
        self::assertSame($expected, $replacementRegister->isReplacementRegistered($original));
    }

    public function isReplacementRegisteredProvider(): array
    {
        return [
            'nothing registered' => [
                'expected' => false,
                'original' => '$a',
                'replacementRegister' => ReplacementRegister::create('$x'),
            ],
            'not registered' => [
                'expected' => false,
                'original' => '$a',
                'replacementRegister' => ReplacementRegister::create('$x')->register('$b'),
            ],
            'registered' => [
                'expected' => true,
                'original' => '$a',
                'replacementRegister' => ReplacementRegister::create('$x')->register('$a'),
            ],
        ];
    }

    /** @dataProvider getReplacementThrowsProvider */
    public function testGetReplacementThrows(string $original, ReplacementRegister $replacementRegister): void
    {
        self::expectException(NoReplacementRegistered::class);

        $replacementRegister->getReplacement($original);
    }

    public function getReplacementThrowsProvider(): array
    {
        return [
            'nothing registered' => [
                'original' => '$a',
                'replacementRegister' => ReplacementRegister::create('$x'),
            ],
            'not registered' => [
                'original' => '$a',
                'replacementRegister' => ReplacementRegister::create('$x')->register('$b'),
            ],
        ];
    }

    /** @dataProvider registerAndGetReplacementProvider */
    public function testRegisterAndGetReplacement(string $expected, string $original, string $replacementPrefix, array $toRegister): void
    {
        $replacementRegister = ReplacementRegister::create($replacementPrefix);
        foreach ($toRegister as $x) {
            $replacementRegister->register($x);
        }

        self::assertSame($expected, $replacementRegister->getReplacement($original));
    }

    public function registerAndGetReplacementProvider(): array
    {
        return [
            'one registered' => [
                'expected' => '$x0',
                'original' => '$a',
                'replacementPrefix' => '$x',
                'toRegister' => ['$a'],
            ],
            'multiple registered, first does not change' => [
                'expected' => '$x0',
                'original' => '$a',
                'replacementPrefix' => '$x',
                'toRegister' => ['$a', '$b'],
            ],
            'multiple registered, get second' => [
                'expected' => '$x1',
                'original' => '$b',
                'replacementPrefix' => '$x',
                'toRegister' => ['$a', '$b'],
            ],
            'other replacementPrefix' => [
                'expected' => 'abc0',
                'original' => '$a',
                'replacementPrefix' => 'abc',
                'toRegister' => ['$a'],
            ],
        ];
    }
}