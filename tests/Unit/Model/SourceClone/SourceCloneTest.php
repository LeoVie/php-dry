<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\SourceClone;

use App\Collection\MethodsCollection;
use App\Model\Method\Method;
use App\Model\SourceClone\SourceClone;
use Generator;
use PHPUnit\Framework\TestCase;

class SourceCloneTest extends TestCase
{
    /** @dataProvider createProvider */
    public function testCreate(string $type): void
    {
        self::assertSame(
            $type,
            SourceClone::create($type, $this->createMock(MethodsCollection::class))->getType()
        );
    }

    public function createProvider(): array
    {
        return [
            SourceClone::TYPE_1 => [SourceClone::TYPE_1],
            SourceClone::TYPE_2 => [SourceClone::TYPE_2],
            SourceClone::TYPE_3 => [SourceClone::TYPE_3],
            SourceClone::TYPE_4 => [SourceClone::TYPE_4],
        ];
    }

    /** @dataProvider getMethodsCollectionProvider */
    public function testGetMethodsCollection(MethodsCollection $expected, SourceClone $sourceClone): void
    {
        self::assertSame($expected, $sourceClone->getMethodsCollection());
    }

    public function getMethodsCollectionProvider(): Generator
    {
        $methodsCollection = $this->createMock(MethodsCollection::class);
        yield [$methodsCollection, SourceClone::create(SourceClone::TYPE_1, $methodsCollection)];
    }

    public function testToString(): void
    {
        $methodsCollection = $this->createMock(MethodsCollection::class);
        $methodsCollection->method('getAll')->willReturn([
            $this->mockMethod('firstMethod'),
            $this->mockMethod('secondMethod'),
        ]);
        self::assertSame(
            "CLONE: Type: TYPE_1, Methods: \n\tfirstMethod\n\tsecondMethod",
            SourceClone::create(SourceClone::TYPE_1, $methodsCollection)->__toString()
        );
    }

    private function mockMethod(string $asString): Method
    {
        $method = $this->createMock(Method::class);
        $method->method('__toString')->willReturn($asString);

        return $method;
    }
}