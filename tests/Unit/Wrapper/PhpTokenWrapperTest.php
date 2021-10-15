<?php

declare(strict_types=1);

namespace App\Tests\Unit\Wrapper;

use App\Wrapper\PhpTokenWrapper;
use PhpToken;
use PHPUnit\Framework\TestCase;

class PhpTokenWrapperTest extends TestCase
{
    /** @dataProvider tokenizeProvider */
    public function testTokenize(string $code): void
    {
        self::assertEquals(PhpToken::tokenize($code), (new PhpTokenWrapper())->tokenize($code));
    }

    public function tokenizeProvider(): array
    {
        return [
            ['<?php'],
            ['<?php $a = 10;'],
        ];
    }
}