<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Collection\MethodsCollection;
use App\Configuration\Configuration;
use App\Factory\TokenSequenceFactory;
use App\Model\Method\Method;
use App\Model\SourceClone\SourceClone;
use App\Service\IgnoreClonesService;
use App\Util\ArrayUtil;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use PHPUnit\Framework\TestCase;

/** @group now */
class IgnoreClonesServiceTest extends TestCase
{
    /** @dataProvider extractNonIgnoredClonesProvider */
    public function testExtractNonIgnoredClones(array $expected, array $clones, array $tokenSequences, Configuration $configuration): void
    {
        $arrayUtil = $this->createMock(ArrayUtil::class);
        $arrayUtil->method('flatten')->willReturn($clones);

        $tokenSequenceFactory = $this->createMock(TokenSequenceFactory::class);
        $tokenSequenceFactory->method('createFromMethod')->willReturnOnConsecutiveCalls(...$tokenSequences);

        self::assertSame(
            $expected,
            (new IgnoreClonesService($arrayUtil, $tokenSequenceFactory))->extractNonIgnoredClones($clones, $configuration)
        );
    }

    public function extractNonIgnoredClonesProvider(): \Generator
    {
        $clones = [
            SourceClone::create(SourceClone::TYPE_1, MethodsCollection::create(
                $this->createMock(Method::class),
            )),
            SourceClone::create(SourceClone::TYPE_1, MethodsCollection::create(
                $this->createMock(Method::class),
            )),
            SourceClone::create(SourceClone::TYPE_1, MethodsCollection::create(
                $this->createMock(Method::class),
            )),
        ];
        $tokenSequences = [
            $this->mockTokenSequence(15),
            $this->mockTokenSequence(10),
            $this->mockTokenSequence(20),
        ];
        $expected = [$clones[0], $clones[2]];
        $configuration = $this->mockConfiguration(11);
        yield 'clones with 1 method each' => [
            'expected' => $expected,
            'clones' => $clones,
            'tokenSequences' => $tokenSequences,
            'configuration' => $configuration,
        ];

        $clones = [
            SourceClone::create(SourceClone::TYPE_1, MethodsCollection::create(
                $this->createMock(Method::class),
                $this->createMock(Method::class),
                $this->createMock(Method::class),
            )),
        ];
        $tokenSequences = [
            $this->mockTokenSequence(15),
            $this->mockTokenSequence(10),
            $this->mockTokenSequence(20),
        ];
        $expected = [$clones[0]];
        $configuration = $this->mockConfiguration(11);
        yield '1 clone with multiple methods' => [
            'expected' => $expected,
            'clones' => $clones,
            'tokenSequences' => $tokenSequences,
            'configuration' => $configuration,
        ];

        $clones = [
            SourceClone::create(SourceClone::TYPE_1, MethodsCollection::create(
                $this->createMock(Method::class),
                $this->createMock(Method::class),
                $this->createMock(Method::class),
            )),
        ];
        $tokenSequences = [
            $this->mockTokenSequence(15),
            $this->mockTokenSequence(10),
            $this->mockTokenSequence(20),
        ];
        $expected = [];
        $configuration = $this->mockConfiguration(21);
        yield '1 clone with multiple methods, every token sequence too short' => [
            'expected' => $expected,
            'clones' => $clones,
            'tokenSequences' => $tokenSequences,
            'configuration' => $configuration,
        ];
    }

    private function mockTokenSequence(int $length): TokenSequence
    {
        $tokenSequence = $this->createMock(TokenSequence::class);
        $tokenSequence->method('length')->willReturn($length);

        return $tokenSequence;
    }

    private function mockConfiguration(int $minTokenLength): Configuration
    {
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('minTokenLength')->willReturn($minTokenLength);

        return $configuration;
    }
}