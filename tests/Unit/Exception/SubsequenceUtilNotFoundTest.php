<?php

declare(strict_types=1);

namespace App\Tests\Unit\Exception;

use App\Exception\SubsequenceUtilNotFound;
use PHPUnit\Framework\TestCase;

class SubsequenceUtilNotFoundTest extends TestCase
{
    public function testCreate(): void
    {
        self::assertSame(
            'No SubsequenceUtil exists for strategy "Foo".',
            SubsequenceUtilNotFound::create('Foo')->getMessage()
        );
    }
}
