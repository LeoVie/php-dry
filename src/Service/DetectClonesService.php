<?php

declare(strict_types=1);

namespace App\Service;

use App\CloneDetection\Type1CloneDetector;
use App\CloneDetection\Type2CloneDetector;
use App\CloneDetection\Type3CloneDetector;
use App\Command\Output\DetectClonesCommandOutput;
use App\Configuration\Configuration;
use App\Exception\CollectionCannotBeEmpty;
use App\Factory\TokenSequenceRepresentative\Type1TokenSequenceRepresentativeFactory;
use App\Factory\TokenSequenceRepresentative\Type2TokenSequenceRepresentativeFactory;
use App\Factory\TokenSequenceRepresentative\Type3TokenSequenceRepresentativeFactory;
use App\File\FindFiles;
use App\Grouper\MethodsBySignatureGrouper;
use App\Model\SourceClone\SourceClone;
use LeoVie\PhpMethodsParser\Exception\NodeTypeNotConvertable;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\StringsException;

class DetectClonesService
{
    public function __construct(
        private FindFiles                               $findFiles,
        private FindMethodsInPathsService               $findMethodsInPathsService,
        private MethodsBySignatureGrouper               $methodsBySignatureGrouper,
        private Type1CloneDetector                      $type1CloneDetector,
        private Type2CloneDetector                      $type2CloneDetector,
        private Type3CloneDetector                      $type3CloneDetector,
        private Type1TokenSequenceRepresentativeFactory $type1TokenSequenceRepresentativeFactory,
        private Type2TokenSequenceRepresentativeFactory $type2TokenSequenceRepresentativeFactory,
        private Type3TokenSequenceRepresentativeFactory $type3TokenSequenceRepresentativeFactory,
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

        $methodsGroupedBySignatures = $this->methodsBySignatureGrouper->group($methods);
        $type1TSRs = $this->type1TokenSequenceRepresentativeFactory->createMultiple($methodsGroupedBySignatures);
        $type2TSRs = $this->type2TokenSequenceRepresentativeFactory->createMultiple($type1TSRs);
        $type3TSRs = $this->type3TokenSequenceRepresentativeFactory->createMultiple($type2TSRs, $configuration);

        return [
            SourceClone::TYPE_1 => $this->type1CloneDetector->detect($type1TSRs),
            SourceClone::TYPE_2 => $this->type2CloneDetector->detect($type2TSRs),
            SourceClone::TYPE_3 => $this->type3CloneDetector->detect($type3TSRs),
        ];
    }
}