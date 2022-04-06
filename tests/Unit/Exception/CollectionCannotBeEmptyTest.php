<?php

declare(strict_types=1);

namespace App\Tests\Unit\Exception;

use App\Exception\CollectionCannotBeEmpty;
use PHPUnit\Framework\TestCase;

class CollectionCannotBeEmptyTest extends TestCase
{
    public function testCreate(): void
    {
        self::assertSame('Collection is not allowed to be empty.', CollectionCannotBeEmpty::create()->getMessage());
    }
}
