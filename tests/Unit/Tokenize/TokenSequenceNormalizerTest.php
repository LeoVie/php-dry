<?php

declare(strict_types=1);

namespace App\Tests\Unit\Tokenize;

use App\Tokenize\Normalize\NothingToNormalizeNormalizer;
use App\Tokenize\TokenSequence;
use App\Tokenize\TokenSequenceNormalizer;
use Iterator;
use PhpToken;
use PHPUnit\Framework\TestCase;

class TokenSequenceNormalizerTest extends TestCase
{
    /** @dataProvider normalizeLevel1Provider */
    public function testNormalizeLevel1(TokenSequence $expected, TokenSequence $tokenSequence): void
    {
        self::assertEquals($expected, (new TokenSequenceNormalizer(
            $this->createMock(Iterator::class),
            $this->createMock(NothingToNormalizeNormalizer::class))
        )->normalizeLevel1($tokenSequence));
    }

    public function normalizeLevel1Provider(): array
    {
        return [
            [
                'expected' => TokenSequence::create([new PhpToken(T_VARIABLE, '')]),
                'tokenSequence' => TokenSequence::create([
                    new PhpToken(T_OPEN_TAG, ''),
                    new PhpToken(T_CLOSE_TAG, ''),
                    new PhpToken(T_VARIABLE, ''),
                    new PhpToken(T_PUBLIC, ''),
                    new PhpToken(T_WHITESPACE, ''),
                    new PhpToken(T_COMMENT, ''),
                    new PhpToken(T_DOC_COMMENT, ''),
                ]),
            ]
        ];
    }
}