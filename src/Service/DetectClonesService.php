<?php

declare(strict_types=1);

namespace App\Service;

use App\CloneDetection\Type1CloneDetector;
use App\CloneDetection\Type2CloneDetector;
use App\CloneDetection\Type3CloneDetector;
use App\CloneDetection\Type4CloneDetector;
use App\Command\Output\DetectClonesCommandOutput;
use App\Configuration\Configuration;
use App\Exception\CollectionCannotBeEmpty;
use App\Factory\SourceCloneCandidate\Type1SourceCloneCandidateFactory;
use App\Factory\SourceCloneCandidate\Type2SourceCloneCandidateFactory;
use App\Factory\SourceCloneCandidate\Type3SourceCloneCandidateFactory;
use App\File\FindFiles;
use App\Grouper\MethodsBySignatureGrouper;
use App\Model\SourceClone\SourceClone;
use LeoVie\PhpMethodsParser\Exception\NodeTypeNotConvertable;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\StringsException;

class DetectClonesService
{
    public function __construct(
        private FindFiles                        $findFiles,
        private FindMethodsInPathsService        $findMethodsInPathsService,
        private MethodsBySignatureGrouper        $methodsBySignatureGrouper,
        private Type1CloneDetector               $type1CloneDetector,
        private Type2CloneDetector               $type2CloneDetector,
        private Type3CloneDetector               $type3CloneDetector,
        private Type1SourceCloneCandidateFactory $type1SourceCloneCandidateFactory,
        private Type2SourceCloneCandidateFactory $type2SourceCloneCandidateFactory,
        private Type3SourceCloneCandidateFactory $type3SourceCloneCandidateFactory,
        private Type4CloneDetector               $type4CloneDetector,
    )
    {
    }

    /**
     * @return SourceClone[][]
     *
     * @throws FilesystemException
     * @throws StringsException
     * @throws NodeTypeNotConvertable
     * @throws CollectionCannotBeEmpty
     */
    public function detectInDirectory(Configuration $configuration, DetectClonesCommandOutput $output): array
    {
        $filePaths = $this->findFiles->findPhpFilesInPath($configuration->directory());

        $output->foundFiles(count($filePaths));

        $methods = $this->findMethodsInPathsService->find($filePaths);

        $output->foundMethods(count($methods));

        $methodSignatureGroups = $this->methodsBySignatureGrouper->group($methods);

        $type1SCCs = $this->type1SourceCloneCandidateFactory->createMultiple($methodSignatureGroups);
        $type1Clones = $this->type1CloneDetector->detect($type1SCCs);

        $type2SCCs = $this->type2SourceCloneCandidateFactory->createMultiple($type1SCCs);
        $type2Clones = $this->type2CloneDetector->detect($type2SCCs);

        $type3SCCs = $this->type3SourceCloneCandidateFactory->createMultiple($type2SCCs, $configuration);
        $type3Clones = $this->type3CloneDetector->detect($type3SCCs);


        $type4Clones = $this->type4CloneDetector->detect($methodSignatureGroups);

        return [
            SourceClone::TYPE_1 => $type1Clones,
            SourceClone::TYPE_2 => $type2Clones,
            SourceClone::TYPE_3 => $type3Clones,
            SourceClone::TYPE_4 => $type4Clones,
        ];
    }
}