<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\CloneDetection\Type1CloneDetector;
use App\CloneDetection\Type2CloneDetector;
use App\Command\Output\DetectClonesCommandOutput;
use App\Configuration\Configuration;
use App\Factory\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentativeFactory;
use App\Factory\TokenSequenceRepresentative\TokenSequenceRepresentativeFactory;
use App\File\FindFiles;
use App\Grouper\MethodsBySignatureGrouper;
use App\Merge\NormalizedTokenSequenceRepresentativeMerger;
use App\Model\SourceClone\SourceClone;
use App\Service\DetectClonesService;
use App\Service\FindMethodsInPathsService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

class DetectClonesServiceTest extends TestCase
{
    public function testDetectInDirectory(): void
    {
        $findFiles = $this->createMock(FindFiles::class);
        $findFiles->method('findPhpFilesInPath')->willReturn([]);

        $findMethodsInPathsService = $this->createMock(FindMethodsInPathsService::class);
        $findMethodsInPathsService->method('find')->willReturn([]);

        $methodsBySignatureGrouper = $this->createMock(MethodsBySignatureGrouper::class);
        $methodsBySignatureGrouper->method('group')->willReturn([]);

        $type1CloneDetector = $this->createMock(Type1CloneDetector::class);
        $type1CloneDetector->method('detect')->willReturn(['type 1 clones']);

        $type2CloneDetector = $this->createMock(Type2CloneDetector::class);
        $type2CloneDetector->method('detect')->willReturn(['type 2 clones']);

        $tokenSequenceRepresentativeFactory = $this->createMock(TokenSequenceRepresentativeFactory::class);
        $tokenSequenceRepresentativeFactory->method('createMultipleForMultipleMethodsCollections')->willReturn([]);

        $normalizedTokenSequenceRepresentativeFactory = $this->createMock(NormalizedTokenSequenceRepresentativeFactory::class);
        $normalizedTokenSequenceRepresentativeFactory->method('normalizeMultipleTokenSequenceRepresentatives')->willReturn([]);

        $normalizedTokenSequenceRepresentativeMerger = $this->createMock(NormalizedTokenSequenceRepresentativeMerger::class);
        $normalizedTokenSequenceRepresentativeMerger->method('merge')->willReturn([]);

        $detectClonesService = new DetectClonesService(
            $findFiles,
            $findMethodsInPathsService,
            $methodsBySignatureGrouper,
            $type1CloneDetector,
            $type2CloneDetector,
            $tokenSequenceRepresentativeFactory,
            $normalizedTokenSequenceRepresentativeFactory,
            $normalizedTokenSequenceRepresentativeMerger,
        );

        $configuration = Configuration::create('', 0, 0);

        $output = $this->createMock(DetectClonesCommandOutput::class);
        $output->method('foundFiles')->willReturnSelf();
        $output->method('foundMethods')->willReturnSelf();

        $expected = [
            SourceClone::TYPE_1 => ['type 1 clones'],
            SourceClone::TYPE_2 => ['type 2 clones'],
        ];

        self::assertSame($expected, $detectClonesService->detectInDirectory($configuration, $output));
    }
}