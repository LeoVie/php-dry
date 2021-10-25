<?php

declare(strict_types=1);

namespace App\Tests\Unit\CloneDetection;

use App\CloneDetection\CloneDetector;
use App\Collection\MethodsCollection;
use App\Model\Method\Method;
use App\Model\SourceClone\SourceClone;
use App\Model\TokenSequenceRepresentative\Type1TokenSequenceRepresentative;
use App\Model\TokenSequenceRepresentative\Type2TokenSequenceRepresentative;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use PHPUnit\Framework\TestCase;

class CloneDetectorTest extends TestCase
{
    /** @dataProvider detectProvider */
    public function testDetect(array $expected, array $tokenSequenceRepresentatives, string $type): void
    {
        self::assertEquals($expected, (new CloneDetector())->detect($tokenSequenceRepresentatives, $type));
    }

    public function detectProvider(): array
    {
        return [
            'no tokenSequenceRepresentatives' => [
                'expected' => [],
                'tokenSequenceRepresentatives' => [],
                'type' => SourceClone::TYPE_1,
            ],
            'no tokenSequenceRepresentatives with > 1 method' => [
                'expected' => [],
                'tokenSequenceRepresentatives' => [
                    Type1TokenSequenceRepresentative::create(
                        $this->createMock(TokenSequence::class),
                        MethodsCollection::create($this->createMock(Method::class))
                    ),
                    Type1TokenSequenceRepresentative::create(
                        $this->createMock(TokenSequence::class),
                        MethodsCollection::create($this->createMock(Method::class))
                    ),
                ],
                'type' => SourceClone::TYPE_1,
            ],
            'only tokenSequenceRepresentatives with > 1 method' => [
                'expected' => [
                    SourceClone::create(
                        SourceClone::TYPE_1,
                        MethodsCollection::create(
                            $this->createMock(Method::class),
                            $this->createMock(Method::class),
                        )
                    ),
                    SourceClone::create(
                        SourceClone::TYPE_1,
                        MethodsCollection::create(
                            $this->createMock(Method::class),
                            $this->createMock(Method::class),
                            $this->createMock(Method::class),
                        )
                    ),
                ],
                'tokenSequenceRepresentatives' => [
                    Type1TokenSequenceRepresentative::create(
                        $this->createMock(TokenSequence::class),
                        MethodsCollection::create(
                            $this->createMock(Method::class),
                            $this->createMock(Method::class),
                        )
                    ),
                    Type1TokenSequenceRepresentative::create(
                        $this->createMock(TokenSequence::class),
                        MethodsCollection::create(
                            $this->createMock(Method::class),
                            $this->createMock(Method::class),
                            $this->createMock(Method::class),
                        )
                    ),
                ],
                'type' => SourceClone::TYPE_1,
            ],
            'other type' => [
                'expected' => [
                    SourceClone::create(
                        SourceClone::TYPE_2,
                        MethodsCollection::create(
                            $this->createMock(Method::class),
                            $this->createMock(Method::class),
                        )
                    ),
                ],
                'tokenSequenceRepresentatives' => [
                    Type2TokenSequenceRepresentative::create(
                        $this->createMock(TokenSequence::class),
                        MethodsCollection::create(
                            $this->createMock(Method::class),
                            $this->createMock(Method::class),
                        )
                    ),
                ],
                'type' => SourceClone::TYPE_2,
            ],
        ];
    }
}