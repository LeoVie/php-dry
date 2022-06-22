<?php

declare(strict_types=1);

namespace App\Tests\Unit\Exception;

use App\Exception\NoParamRequestForParamType;
use PHPUnit\Framework\TestCase;

class NoParamRequestForParamTypeTest extends TestCase
{
    public function testCreate(): void
    {
        self::assertSame(
            'No ParamRequest exists for param type "Foo" (class "Bar").',
            NoParamRequestForParamType::create('Foo', 'Bar')->getMessage()
        );
    }
}
