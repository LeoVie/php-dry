<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\SourceClone;

use App\Collection\MethodsCollection;
use App\Model\SourceClone\SourceClone;
use Generator;
use PHPUnit\Framework\TestCase;

class SourceCloneTest extends TestCase
{
    public function testCreateType1(): void
    {
        self::assertSame(SourceClone::TYPE_1, SourceClone::createType1($this->createMock(MethodsCollection::class))->getType());
    }

    public function testCreateType2(): void
    {
        self::assertSame(SourceClone::TYPE_2, SourceClone::createType2($this->createMock(MethodsCollection::class))->getType());
    }

    public function testCreateType3(): void
    {
        self::assertSame(SourceClone::TYPE_3, SourceClone::createType3($this->createMock(MethodsCollection::class))->getType());
    }

    public function testCreateType4(): void
    {
        self::assertSame(SourceClone::TYPE_4, SourceClone::createType4($this->createMock(MethodsCollection::class))->getType());
    }

    /** @dataProvider getMethodsCollectionProvider */
    public function testGetMethodsCollection(MethodsCollection $expected, SourceClone $sourceClone): void
    {
        self::assertSame($expected, $sourceClone->getMethodsCollection());
    }

    public function getMethodsCollectionProvider(): Generator
    {
        $methodsCollection = $this->createMock(MethodsCollection::class);
        yield [$methodsCollection, SourceClone::createType1($methodsCollection)];
    }
}