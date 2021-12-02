<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\CloneDetection\Type1CloneDetector;
use App\CloneDetection\Type2CloneDetector;
use App\CloneDetection\Type3CloneDetector;
use App\CloneDetection\Type4CloneDetector;
use App\Command\Output\DetectClonesCommandOutput;
use App\Configuration\Configuration;
use App\Factory\SourceCloneCandidate\Type1SourceCloneCandidateFactory;
use App\Factory\SourceCloneCandidate\Type2SourceCloneCandidateFactory;
use App\Factory\SourceCloneCandidate\Type3SourceCloneCandidateFactory;
use App\Factory\SourceCloneCandidate\Type4SourceCloneCandidateFactory;
use App\File\FindFiles;
use App\Grouper\MethodsBySignatureGrouper;
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

        $type4CloneDetector = $this->createMock(Type4CloneDetector::class);
        $type4CloneDetector->method('detect')->willReturn(['type 4 clones']);

        $type1SourceCloneCandidateFactory = $this->createMock(Type1SourceCloneCandidateFactory::class);
        $type1SourceCloneCandidateFactory->method('createMultiple')->willReturn([]);

        $type2SourceCloneCandidateFactory = $this->createMock(Type2SourceCloneCandidateFactory::class);
        $type2SourceCloneCandidateFactory->method('createMultiple')->willReturn([]);

        $type3SourceCloneCandidateFactory = $this->createMock(Type3SourceCloneCandidateFactory::class);
        $type3SourceCloneCandidateFactory->method('createMultiple')->willReturn([]);

        $type4SourceCloneCandidateFactory = $this->createMock(Type4SourceCloneCandidateFactory::class);
        $type4SourceCloneCandidateFactory->method('createMultiple')->willReturn([]);

        $detectClonesService = new DetectClonesService(
            $findFiles,
            $findMethodsInPathsService,
            $methodsBySignatureGrouper,
            $type1CloneDetector,
            $type2CloneDetector,
            $type3CloneDetector,
            $type4CloneDetector,
            $type1SourceCloneCandidateFactory,
            $type2SourceCloneCandidateFactory,
            $type3SourceCloneCandidateFactory,
            $type4SourceCloneCandidateFactory,
        );

        $configuration = Configuration::create('', 0, 0, '');

        $output = $this->createMock(DetectClonesCommandOutput::class);
        $output->method('foundFiles')->willReturnSelf();
        $output->method('foundMethods')->willReturnSelf();

        $expected = [
            SourceClone::TYPE_1 => ['type 1 clones'],
            SourceClone::TYPE_2 => ['type 2 clones'],
            SourceClone::TYPE_3 => ['type 3 clones'],
            SourceClone::TYPE_4 => ['type 4 clones'],
        ];

        self::assertSame($expected, $detectClonesService->detectInDirectory($configuration, $output));
    }
}