<?php

declare(strict_types=1);

namespace App\Tests\Unit\Grouper;

use App\Grouper\MethodTokenSequencesByTokenSequencesGrouper;
use LeoVie\PhpGrouper\Service\Grouper;
use PHPUnit\Framework\TestCase;

class MethodTokenSequencesByTokenSequencesGrouperTest extends TestCase
{
    public function testGroup(): void
    {
        $identityGrouper = $this->createMock(Grouper::class);
        $identityGrouper->method('groupByGroupID')->willReturn(['grouped']);

        self::assertSame(['grouped'], (new MethodTokenSequencesByTokenSequencesGrouper($identityGrouper))->group([]));
    }
}