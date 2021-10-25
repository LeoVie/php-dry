<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\CloneDetection\Type1CloneDetector;
use App\CloneDetection\Type2CloneDetector;
use App\CloneDetection\Type3CloneDetector;
use App\Command\Output\DetectClonesCommandOutput;
use App\Configuration\Configuration;
use App\Factory\TokenSequenceRepresentative\Type2TokenSequenceRepresentativeFactory;
use App\Factory\TokenSequenceRepresentative\Type1TokenSequenceRepresentativeFactory;
use App\Factory\TokenSequenceRepresentative\Type3TokenSequenceRepresentativeFactory;
use App\File\FindFiles;
use App\Grouper\MethodsBySignatureGrouper;
use App\Merge\Type2TokenSequenceRepresentativeMerger;
use App\Model\SourceClone\SourceClone;
use App\Service\DetectClonesService;
use App\Service\FindMethodsInPathsService;
use PHPUnit\Framework\TestCase;

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

        $type3CloneDetector = $this->createMock(Type3CloneDetector::class);
        $type3CloneDetector->method('detect')->willReturn(['type 3 clones']);

        $type1TokenSequenceRepresentativeFactory = $this->createMock(Type1TokenSequenceRepresentativeFactory::class);
        $type1TokenSequenceRepresentativeFactory->method('createMultipleForMultipleMethodsCollections')->willReturn([]);

        $type2TokenSequenceRepresentativeFactory = $this->createMock(Type2TokenSequenceRepresentativeFactory::class);
        $type2TokenSequenceRepresentativeFactory->method('createMultiple')->willReturn([]);

        $type3TokenSequenceRepresentativeFactory = $this->createMock(Type3TokenSequenceRepresentativeFactory::class);
        $type3TokenSequenceRepresentativeFactory->method('createMultiple')->willReturn([]);

        $detectClonesService = new DetectClonesService(
            $findFiles,
            $findMethodsInPathsService,
            $methodsBySignatureGrouper,
            $type1CloneDetector,
            $type2CloneDetector,
            $type3CloneDetector,
            $type1TokenSequenceRepresentativeFactory,
            $type2TokenSequenceRepresentativeFactory,
            $type3TokenSequenceRepresentativeFactory
        );

        $configuration = Configuration::create('', 0, 0);

        $output = $this->createMock(DetectClonesCommandOutput::class);
        $output->method('foundFiles')->willReturnSelf();
        $output->method('foundMethods')->willReturnSelf();

        $expected = [
            SourceClone::TYPE_1 => ['type 1 clones'],
            SourceClone::TYPE_2 => ['type 2 clones'],
            SourceClone::TYPE_3 => ['type 3 clones'],
        ];

        self::assertSame($expected, $detectClonesService->detectInDirectory($configuration, $output));
    }
}