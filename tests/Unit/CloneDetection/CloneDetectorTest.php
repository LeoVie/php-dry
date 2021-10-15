<?php

declare(strict_types=1);

namespace App\Tests\Unit\CloneDetection;

use App\CloneDetection\CloneDetector;
use App\Collection\MethodsCollection;
use App\Model\Method\Method;
use App\Model\SourceClone\SourceClone;
use App\Model\TokenSequenceRepresentative\ExactTokenSequenceRepresentative;
use App\Model\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentative;
use App\Tokenize\TokenSequence;
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
                    ExactTokenSequenceRepresentative::create(
                        $this->createMock(TokenSequence::class),
                        MethodsCollection::empty()
                    ),
                    ExactTokenSequenceRepresentative::create(
                        $this->createMock(TokenSequence::class),
                        MethodsCollection::withInitialContent($this->createMock(Method::class))
                    ),
                ],
                'type' => SourceClone::TYPE_1,
            ],
            'only tokenSequenceRepresentatives with > 1 method' => [
                'expected' => [
                    SourceClone::create(
                        SourceClone::TYPE_1,
                        MethodsCollection::withInitialContent(
                            $this->createMock(Method::class),
                            $this->createMock(Method::class),
                        )
                    ),
                    SourceClone::create(
                        SourceClone::TYPE_1,
                        MethodsCollection::withInitialContent(
                            $this->createMock(Method::class),
                            $this->createMock(Method::class),
                            $this->createMock(Method::class),
                        )
                    ),
                ],
                'tokenSequenceRepresentatives' => [
                    ExactTokenSequenceRepresentative::create(
                        $this->createMock(TokenSequence::class),
                        MethodsCollection::withInitialContent(
                            $this->createMock(Method::class),
                            $this->createMock(Method::class),
                        )
                    ),
                    ExactTokenSequenceRepresentative::create(
                        $this->createMock(TokenSequence::class),
                        MethodsCollection::withInitialContent(
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
                        MethodsCollection::withInitialContent(
                            $this->createMock(Method::class),
                            $this->createMock(Method::class),
                        )
                    ),
                ],
                'tokenSequenceRepresentatives' => [
                    NormalizedTokenSequenceRepresentative::create(
                        $this->createMock(TokenSequence::class),
                        MethodsCollection::withInitialContent(
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