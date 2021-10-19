<?php

declare(strict_types=1);

namespace App\Service;

use App\CloneDetection\Type1CloneDetector;
use App\CloneDetection\Type2CloneDetector;
use App\Command\Output\DetectClonesCommandOutput;
use App\Configuration\Configuration;
use App\Exception\CollectionCannotBeEmpty;
use App\Exception\NodeTypeNotConvertable;
use App\Factory\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentativeFactory;
use App\Factory\TokenSequenceRepresentative\TokenSequenceRepresentativeFactory;
use App\File\FindFiles;
use App\Grouper\MethodsBySignatureGrouper;
use App\Merge\NormalizedTokenSequenceRepresentativeMerger;
use App\Model\SourceClone\SourceClone;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\StringsException;
use Symfony\Component\Stopwatch\Stopwatch;

class DetectClonesService
{
    public function __construct(
        private FindFiles                                    $findFiles,
        private FindMethodsInPathsService                    $findMethodsInPathsService,
        private MethodsBySignatureGrouper                    $methodsBySignatureGrouper,
        private Type1CloneDetector                           $type1CloneDetector,
        private Type2CloneDetector                           $type2CloneDetector,
        private TokenSequenceRepresentativeFactory           $tokenSequenceRepresentativeFactory,
        private NormalizedTokenSequenceRepresentativeFactory $normalizedTokenSequenceRepresentativeFactory,
        private NormalizedTokenSequenceRepresentativeMerger  $normalizedTokenSequenceRepresentativeMerger,
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
        $tokenSequenceRepresentatives
            = $this->tokenSequenceRepresentativeFactory->createMultipleForMultipleMethodsCollections($methodsGroupedBySignatures);
        $normalizedTokenSequenceRepresentatives
            = $this->normalizedTokenSequenceRepresentativeFactory->normalizeMultipleTokenSequenceRepresentatives($tokenSequenceRepresentatives);

        return [
            SourceClone::TYPE_1 => $this->type1CloneDetector->detect($tokenSequenceRepresentatives),
            SourceClone::TYPE_2 => $this->type2CloneDetector->detect(
                $this->normalizedTokenSequenceRepresentativeMerger->merge($normalizedTokenSequenceRepresentatives)
            ),
        ];
    }
}