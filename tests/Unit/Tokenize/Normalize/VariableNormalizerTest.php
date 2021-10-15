<?php

declare(strict_types=1);

namespace App\Tests\Unit\Tokenize\Normalize;

use App\Tokenize\Normalize\VariableNormalizer;
use PhpToken;
use PHPUnit\Framework\TestCase;

class VariableNormalizerTest extends TestCase
{
    /** @dataProvider supportsProvider */
    public function testSupports(bool $expected, PhpToken $token): void
    {
        self::assertSame($expected, (new VariableNormalizer())->supports($token));
    }

    public function supportsProvider(): array
    {
        return [
            'T_VARIABLE' => [
                true,
                new PhpToken(T_VARIABLE, ''),
            ],
            'T_LNUMBER' => [
                false,
                new PhpToken(T_LNUMBER, ''),
            ],
        ];
    }

    public function testReset(): void
    {
        $normalizer = new VariableNormalizer();
        self::assertSame($normalizer, $normalizer->reset());
    }

    /** @dataProvider normalizeProvider */
    public function testNormalize(PhpToken $expected, PhpToken $token): void
    {
        self::assertEquals($expected, (new VariableNormalizer())->normalizeToken($token));
    }

    public function normalizeProvider(): array
    {
        return [
            [
                'expected' => new PhpToken(T_VARIABLE, '$x0', 10, 20),
                'token' => new PhpToken(T_VARIABLE, '$foo', 10, 20),
            ],
            [
                'expected' => new PhpToken(T_VARIABLE, '$x0', 199, 71),
                'token' => new PhpToken(T_VARIABLE, '$bar', 199, 71),
            ],
        ];
    }
}