<?php

declare(strict_types=1);

namespace App\Tests\Unit\CloneDetection;

use App\CloneDetection\CloneDetector;
use App\CloneDetection\Type1CloneDetector;
use App\Collection\MethodsCollection;
use App\Model\SourceClone\SourceClone;
use PHPUnit\Framework\TestCase;

class Type1CloneDetectorTest extends TestCase
{
    public function testDetect(): void
    {
        $clones = [SourceClone::create(SourceClone::TYPE_1, MethodsCollection::empty())];

        $expected = $clones;

        $cloneDetector = $this->createMock(CloneDetector::class);
        $cloneDetector->method('detect')->willReturn($clones);

        self::assertSame($expected, (new Type1CloneDetector($cloneDetector))->detect([]));
    }
}