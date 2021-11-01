<?php

declare(strict_types=1);

namespace App\Service;

use App\CloneDetection\Type1CloneDetector;
use App\CloneDetection\Type2CloneDetector;
use App\CloneDetection\Type3CloneDetector;
use App\CloneDetection\Type4CloneDetector;
use App\Collection\MethodsCollection;
use App\Command\Output\DetectClonesCommandOutput;
use App\Configuration\Configuration;
use App\Exception\CollectionCannotBeEmpty;
use App\Factory\TokenSequenceRepresentative\Type1TokenSequenceRepresentativeFactory;
use App\Factory\TokenSequenceRepresentative\Type2TokenSequenceRepresentativeFactory;
use App\Factory\TokenSequenceRepresentative\Type3TokenSequenceRepresentativeFactory;
use App\File\FindFiles;
use App\Grouper\MethodsBySignatureGrouper;
use App\Model\Method\MethodSignatureGroup;
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
        private Type4CloneDetector                      $type4CloneDetector,
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

        $methodsGroupedBySignatures = $this->methodsBySignatureGrouper->groupOld($methods);

        $methodSignatureGroups = $this->methodsBySignatureGrouper->group($methods);

        print("\n\n\n");
        print(join("\n\n", array_map(fn(MethodSignatureGroup $mg): string => $mg->__toString(), $methodSignatureGroups)));
        print("\n");
        die;

        $type1TSRs = $this->type1TokenSequenceRepresentativeFactory->createMultiple($methodsGroupedBySignatures);
        $type1Clones = $this->type1CloneDetector->detect($type1TSRs);

        $type2TSRs = $this->type2TokenSequenceRepresentativeFactory->createMultiple($type1TSRs);
        $type2Clones = $this->type2CloneDetector->detect($type2TSRs);

        $type3TSRs = $this->type3TokenSequenceRepresentativeFactory->createMultiple($type2TSRs, $configuration);
        $type3Clones = $this->type3CloneDetector->detect($type3TSRs);

        return [
            SourceClone::TYPE_1 => $type1Clones,
            SourceClone::TYPE_2 => $type2Clones,
            SourceClone::TYPE_3 => $type3Clones,
        ];
    }
}