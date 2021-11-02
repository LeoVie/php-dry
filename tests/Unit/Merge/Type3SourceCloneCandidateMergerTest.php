<?php

declare(strict_types=1);

namespace App\Tests\Unit\Merge;

use App\Collection\MethodsCollection;
use App\Merge\Type3SourceCloneCandidatesMerger;
use App\Model\Method\Method;
use App\Model\SourceCloneCandidate\Type3SourceCloneCandidate;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use PHPUnit\Framework\TestCase;

class Type3SourceCloneCandidateMergerTest extends TestCase
{
    /** @dataProvider mergeProvider */
    public function testMerge(array $expected, array $groups): void
    {
        self::assertEquals($expected, (new Type3SourceCloneCandidatesMerger())->merge($groups));
    }

    public function mergeProvider(): array
    {
        $tokenSequences = [
            $this->createMock(TokenSequence::class),
            $this->createMock(TokenSequence::class),
            $this->createMock(TokenSequence::class),
            $this->createMock(TokenSequence::class),
            $this->createMock(TokenSequence::class),
        ];

        $methods = [
            $this->createMock(Method::class),
            $this->createMock(Method::class),
            $this->createMock(Method::class),
            $this->createMock(Method::class),
            $this->createMock(Method::class),
        ];

        $groups = [
            [
                Type3SourceCloneCandidate::create(
                    [$tokenSequences[0], $tokenSequences[1]],
                    MethodsCollection::create($methods[0], $methods[1])
                ),
                Type3SourceCloneCandidate::create(
                    [$tokenSequences[2]],
                    MethodsCollection::create($methods[2])
                ),
            ],
            [
                Type3SourceCloneCandidate::create(
                    [$tokenSequences[3], $tokenSequences[4]],
                    MethodsCollection::create($methods[3], $methods[4])
                ),
            ],
        ];

        $expected = [
            Type3SourceCloneCandidate::create(
                [$tokenSequences[0], $tokenSequences[1], $tokenSequences[2]],
                MethodsCollection::create($methods[0], $methods[1], $methods[2])
            ),
            Type3SourceCloneCandidate::create(
                [$tokenSequences[3], $tokenSequences[4]],
                MethodsCollection::create($methods[3], $methods[4])
            ),
        ];

        return [
            [
                'expected' => $expected,
                'groups' => $groups,
            ]
        ];
    }
}