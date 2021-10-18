<?php

declare(strict_types=1);

namespace App\Tests\Unit\Tokenize;

use App\Tokenize\Normalize\NothingToNormalizeNormalizer;
use App\Tokenize\Normalize\TokenNormalizer;
use App\Tokenize\TokenSequence;
use App\Tokenize\TokenSequenceNormalizer;
use ArrayIterator;
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
        $tokenToBeLeft = new PhpToken(T_VARIABLE, '');
        return [
            [
                'expected' => TokenSequence::create([$tokenToBeLeft]),
                'tokenSequence' => TokenSequence::create([
                    new PhpToken(T_OPEN_TAG, ''),
                    new PhpToken(T_CLOSE_TAG, ''),
                    $tokenToBeLeft,
                    new PhpToken(T_PUBLIC, ''),
                    new PhpToken(T_WHITESPACE, ''),
                    new PhpToken(T_COMMENT, ''),
                    new PhpToken(T_DOC_COMMENT, ''),
                ]),
            ],
        ];
    }

    /** @dataProvider normalizeLevel2Provider */
    public function testNormalizeLevel2(TokenSequence $expected, TokenSequence $tokenSequence): void
    {
        $variableNormalizer = $this->mockTokenNormalizer(
            fn(PhpToken $token): bool => $token->id === T_VARIABLE,
            fn(PhpToken $token) => new PhpToken($token->id, 'NORMALIZED_VARIABLE_' . $token->text),
        );

        $lNumberNormalizer = $this->mockTokenNormalizer(
            fn(PhpToken $token): bool => $token->id === T_LNUMBER,
            fn(PhpToken $token) => new PhpToken($token->id, 'NORMALIZED_LNUMBER_' . $token->text),
        );

        $nothingToNormalizeNormalizer = $this->createMock(NothingToNormalizeNormalizer::class);
        $nothingToNormalizeNormalizer->method('supports')->willReturnCallback(fn(PhpToken $token): bool => true);
        $nothingToNormalizeNormalizer->method('normalizeToken')->willReturnCallback(fn(PhpToken $token) => new PhpToken($token->id, 'NOT_NORMALIZED_' . $token->text));

        $tokenNormalizers = new ArrayIterator([$variableNormalizer, $lNumberNormalizer]);

        self::assertEquals($expected, (new TokenSequenceNormalizer($tokenNormalizers, $nothingToNormalizeNormalizer))->normalizeLevel2($tokenSequence));
    }

    public function normalizeLevel2Provider(): array
    {
        return [
            [
                'expected' => TokenSequence::create([
                    new PhpToken(T_OPEN_TAG, 'NOT_NORMALIZED_<?php'),
                    new PhpToken(T_CLOSE_TAG, 'NOT_NORMALIZED_?>'),
                    new PhpToken(T_VARIABLE, 'NORMALIZED_VARIABLE_$a'),
                    new PhpToken(T_PUBLIC, 'NOT_NORMALIZED_public'),
                    new PhpToken(T_LNUMBER, 'NORMALIZED_LNUMBER_700'),
                    new PhpToken(T_WHITESPACE, 'NOT_NORMALIZED_ '),
                    new PhpToken(T_COMMENT, 'NOT_NORMALIZED_// foo'),
                    new PhpToken(T_DOC_COMMENT, 'NOT_NORMALIZED_/** bar */'),
                ]),
                'tokenSequence' => TokenSequence::create([
                    new PhpToken(T_OPEN_TAG, '<?php'),
                    new PhpToken(T_CLOSE_TAG, '?>'),
                    new PhpToken(T_VARIABLE, '$a'),
                    new PhpToken(T_PUBLIC, 'public'),
                    new PhpToken(T_LNUMBER, '700'),
                    new PhpToken(T_WHITESPACE, ' '),
                    new PhpToken(T_COMMENT, '// foo'),
                    new PhpToken(T_DOC_COMMENT, '/** bar */'),
                ]),
            ],
        ];
    }

    private function mockTokenNormalizer(callable $supports, callable $normalizeToken): TokenNormalizer
    {
        $normalizer = $this->createMock(TokenNormalizer::class);
        $normalizer->method('supports')->willReturnCallback($supports);
        $normalizer->method('normalizeToken')->willReturnCallback($normalizeToken);

        return $normalizer;
    }
}