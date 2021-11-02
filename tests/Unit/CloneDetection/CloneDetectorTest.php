<?php

declare(strict_types=1);

namespace App\Tests\Unit\CloneDetection;

use App\CloneDetection\CloneDetector;
use App\Collection\MethodsCollection;
use App\Model\Method\Method;
use App\Model\SourceClone\SourceClone;
use App\Model\SourceCloneCandidate\Type1SourceCloneCandidate;
use App\Model\SourceCloneCandidate\Type2SourceCloneCandidate;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use PHPUnit\Framework\TestCase;

class CloneDetectorTest extends TestCase
{
    /** @dataProvider detectProvider */
    public function testDetect(array $expected, array $sourceCloneCandidates, string $type): void
    {
        self::assertEquals($expected, (new CloneDetector())->detect($sourceCloneCandidates, $type));
    }

    public function detectProvider(): array
    {
        return [
            'no sourceCloneCandidates' => [
                'expected' => [],
                'sourceCloneCandidates' => [],
                'type' => SourceClone::TYPE_1,
            ],
            'no sourceCloneCandidates with > 1 method' => [
                'expected' => [],
                'sourceCloneCandidates' => [
                    Type1SourceCloneCandidate::create(
                        $this->createMock(TokenSequence::class),
                        MethodsCollection::create($this->createMock(Method::class))
                    ),
                    Type1SourceCloneCandidate::create(
                        $this->createMock(TokenSequence::class),
                        MethodsCollection::create($this->createMock(Method::class))
                    ),
                ],
                'type' => SourceClone::TYPE_1,
            ],
            'only sourceCloneCandidates with > 1 method' => [
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
                'sourceCloneCandidates' => [
                    Type1SourceCloneCandidate::create(
                        $this->createMock(TokenSequence::class),
                        MethodsCollection::create(
                            $this->createMock(Method::class),
                            $this->createMock(Method::class),
                        )
                    ),
                    Type1SourceCloneCandidate::create(
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
                'sourceCloneCandidates' => [
                    Type2SourceCloneCandidate::create(
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