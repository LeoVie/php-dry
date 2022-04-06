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

    private function mockJsonSerializableMethod(array $asJson): Method
    {
        $method = $this->createMock(Method::class);
        $method->method('jsonSerialize')->willReturn($asJson);

        return $method;
    }

    public function testJsonSerialize(): void
    {
        $methodsCollection = $this->createMock(MethodsCollection::class);
        $methodsCollection->method('getAll')->willReturn([
            $this->mockJsonSerializableMethod(['name' => 'firstMethod']),
            $this->mockJsonSerializableMethod(['name' => 'secondMethod']),
        ]);
        self::assertJsonStringEqualsJsonString(
            \Safe\json_encode([
                'type' => 'TYPE_1',
                'methods' => [
                    [
                        'name' => 'firstMethod',
                    ],
                    [
                        'name' => 'secondMethod',
                    ],
                ],
            ]),
            \Safe\json_encode(SourceClone::create(SourceClone::TYPE_1, $methodsCollection))
        );
    }
}
