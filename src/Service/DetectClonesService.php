<?php

declare(strict_types=1);

namespace App\Service;

use App\CloneDetection\Type1CloneDetector;
use App\CloneDetection\Type2CloneDetector;
use App\Command\Output\DetectClonesCommandOutput;
use App\Exception\NodeTypeNotConvertable;
use App\Factory\TokenSequenceRepresentative\NormalizedTokenSequenceRepresentativeFactory;
use App\Factory\TokenSequenceRepresentative\TokenSequenceRepresentativeFactory;
use App\File\FindFiles;
use App\Grouper\MethodsBySignatureGrouper;
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
    )
    {
    }

    /**
     * @return SourceClone[][]
     *
     * @throws FilesystemException
     * @throws StringsException
     * @throws NodeTypeNotConvertable
     */
    public function detectInDirectory(Stopwatch $stopwatch, string $directory, int $countOfParamSets, DetectClonesCommandOutput $output): array
    {
        $filePaths = $this->findFiles->findPhpFilesInPath($directory);

        $output->single(\Safe\sprintf('Found %s files: %s.', count($filePaths), $stopwatch->lap('detect-clones')));

        $methods = $this->findMethodsInPathsService->find($filePaths);

        $output->single(\Safe\sprintf('Found %s methods: %s', count($methods), $stopwatch->lap('detect-clones')));

        $methodsGroupedBySignatures = $this->methodsBySignatureGrouper->group($methods);
        $tokenSequenceRepresentatives
            = $this->tokenSequenceRepresentativeFactory->createMultipleForMultipleMethodsCollections($methodsGroupedBySignatures);
        $normalizedTokenSequenceRepresentatives
            = $this->normalizedTokenSequenceRepresentativeFactory->normalizeMultipleTokenSequenceRepresentatives($tokenSequenceRepresentatives);

        return [
            SourceClone::TYPE_1 => $this->type1CloneDetector->detect($tokenSequenceRepresentatives),
            SourceClone::TYPE_2 => $this->type2CloneDetector->detect($normalizedTokenSequenceRepresentatives),
        ];
    }
}