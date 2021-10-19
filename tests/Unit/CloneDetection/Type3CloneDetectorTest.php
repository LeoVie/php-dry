<?php

declare(strict_types=1);

namespace App\Tests\Unit\CloneDetection;

use App\CloneDetection\CloneDetector;
use App\CloneDetection\Type3CloneDetector;
use App\Collection\MethodsCollection;
use App\Model\SourceClone\SourceClone;
use PHPUnit\Framework\TestCase;

class Type3CloneDetectorTest extends TestCase
{
    public function testDetect(): void
    {
        $clones = [SourceClone::create(SourceClone::TYPE_3, $this->createMock(MethodsCollection::class))];

        $expected = $clones;

        $cloneDetector = $this->createMock(CloneDetector::class);
        $cloneDetector->method('detect')->willReturn($clones);

        self::assertSame($expected, (new Type3CloneDetector($cloneDetector))->detect([]));
    }
}