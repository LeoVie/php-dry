<?php

declare(strict_types=1);

namespace App\Tests\Unit\Parse;

use App\Parse\LineAndColumnLexerWrapper;
use PhpParser\Lexer;
use PHPUnit\Framework\TestCase;

class LineAndColumnLexerWrapperTest extends TestCase
{
    public function testGetLexer(): void
    {
        $lexer = $this->createMock(Lexer::class);

        self::assertSame($lexer, (new LineAndColumnLexerWrapper($lexer))->getLexer());
    }
}