<?php

declare(strict_types=1);

namespace App\Tests\Unit\Grouper;

use App\Grouper\IdentityGrouper;
use App\Grouper\MethodTokenSequencesByTokenSequencesGrouper;
use PHPUnit\Framework\TestCase;

class MethodTokenSequencesByTokenSequencesGrouperTest extends TestCase
{
    public function testGroup(): void
    {
        $identityGrouper = $this->createMock(IdentityGrouper::class);
        $identityGrouper->method('group')->willReturn(['grouped']);

        self::assertSame(['grouped'], (new MethodTokenSequencesByTokenSequencesGrouper($identityGrouper))->group([]));
    }
}