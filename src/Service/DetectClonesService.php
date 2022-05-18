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
use App\Exception\PhpDocumentorFailed;
use App\Exception\SubsequenceUtilNotFound;
use App\Factory\SourceCloneCandidate\Type1SourceCloneCandidateFactory;
use App\Factory\SourceCloneCandidate\Type2SourceCloneCandidateFactory;
use App\Factory\SourceCloneCandidate\Type3SourceCloneCandidateFactory;
use App\Factory\SourceCloneCandidate\Type4SourceCloneCandidateFactory;
use App\Grouper\MethodsBySignatureGrouper;
use App\Model\Method\Method;
use App\Model\Method\MethodSignatureGroup;
use App\Model\SourceClone\SourceClone;
use App\Model\SourceCloneCandidate\SourceCloneCandidate;
use App\Model\SourceCloneCandidate\Type1SourceCloneCandidate;
use App\Model\SourceCloneCandidate\Type2SourceCloneCandidate;
use LeoVie\PhpConstructNormalize\Service\ConstructNormalizeService;
use LeoVie\PhpMethodModifier\Exception\MethodCannotBeModifiedToNonClassContext;
use LeoVie\PhpParamGenerator\Exception\NoParamGeneratorFoundForParamRequest;
use Safe\Exceptions\FilesystemException;

class DetectClonesService
{
    public function __construct(
        private FindMethodsInPathsService        $findMethodsInPathsService,
        private MethodsBySignatureGrouper        $methodsBySignatureGrouper,
        private Type1CloneDetector               $type1CloneDetector,
        private Type2CloneDetector               $type2CloneDetector,
        private Type3CloneDetector               $type3CloneDetector,
        private Type4CloneDetector               $type4CloneDetector,
        private Type1SourceCloneCandidateFactory $type1SourceCloneCandidateFactory,
        private Type2SourceCloneCandidateFactory $type2SourceCloneCandidateFactory,
        private Type3SourceCloneCandidateFactory $type3SourceCloneCandidateFactory,
        private Type4SourceCloneCandidateFactory $type4SourceCloneCandidateFactory,
        private ConstructNormalizeService        $constructNormalizeService,
        private PhpDocumentorRunner              $phpDocumentorRunner,
    )
    {
    }

    /**
     * @return SourceClone[][]
     *
     * @throws CollectionCannotBeEmpty
     * @throws FilesystemException
     * @throws MethodCannotBeModifiedToNonClassContext
     * @throws NoParamGeneratorFoundForParamRequest
     * @throws SubsequenceUtilNotFound
     * @throws PhpDocumentorFailed
     */
    public function detectInDirectory(DetectClonesCommandOutput $output): array
    {
        $this->phpDocumentorRunner->run();

        $methods = $this->findMethodsInPathsService->findAll();
        $output->foundMethods(count($methods));

        $methodSignatureGroups = $this->methodsBySignatureGrouper->group($methods);

        return $this->detectClones($methodSignatureGroups, $output);
    }

    /**
     * @param MethodSignatureGroup[] $methodSignatureGroups
     *
     * @return array<string, SourceClone[]>
     *
     * @throws CollectionCannotBeEmpty
     * @throws FilesystemException
     * @throws NoParamGeneratorFoundForParamRequest
     * @throws MethodCannotBeModifiedToNonClassContext
     * @throws SubsequenceUtilNotFound
     */
    private function detectClones(array $methodSignatureGroups, DetectClonesCommandOutput $output, bool $includeType4Clones = true): array
    {
        $output->detectionRunningForType('1');
        $type1SCCs = $this->type1SourceCloneCandidateFactory->createMultiple(
            $output->createProgressBarIterator($methodSignatureGroups)
        );
        $type1Clones = $this->type1CloneDetector->detect($type1SCCs);

        $output->newLine()->detectionRunningForType('2');

        /** @var Type1SourceCloneCandidate[] $filteredType1SCCs */
        $filteredType1SCCs = $this->removeSCCsFullyCoveredByCloneAndMethodSignatureGroup($methodSignatureGroups, $type1SCCs, $type1Clones);
        $type2SCCs = $this->type2SourceCloneCandidateFactory->createMultiple(
            $output->createProgressBarIterator($filteredType1SCCs)
        );
        $type2Clones = $this->type2CloneDetector->detect($type2SCCs);

        $output->newLine()->detectionRunningForType('3');

        /** @var Type2SourceCloneCandidate[] $filteredType2SCCs */
        $filteredType2SCCs = $this->removeSCCsFullyCoveredByCloneAndMethodSignatureGroup($methodSignatureGroups, $type2SCCs, $type2Clones);
        $type3SCCs = $this->type3SourceCloneCandidateFactory->createMultiple(
            $output->createProgressBarIterator($filteredType2SCCs)
        );
        $type3Clones = $this->type3CloneDetector->detect($type3SCCs);

        if (!$includeType4Clones) {
            return [
                SourceClone::TYPE_1 => $type1Clones,
                SourceClone::TYPE_2 => $type2Clones,
                SourceClone::TYPE_3 => $type3Clones,
            ];
        }

        $filteredMethodSignatureGroups = $this->removeMethodSignatureGroupsFullyCoveredByClonesAlready($methodSignatureGroups, $type1Clones, $type2Clones, $type3Clones);

        $type4ClonesByConstructNormalization = [];
        $configuration = Configuration::instance();
        if ($configuration->getEnableConstructNormalization()) {
            $output->newLine()->detectionRunningForType('4 by construct normalization');

            $type4ClonesByConstructNormalization = $this->detectType4ClonesByConstructNormalization(
                $output->createProgressBarIterator($filteredMethodSignatureGroups),
                $output,
            );
        }

        $output->newLine()->detectionRunningForType('4 by running');

        $type4SCCS = $this->type4SourceCloneCandidateFactory->createMultipleByRunningMethods(
            $output->createProgressBarIterator($filteredMethodSignatureGroups)
        );
        $type4ClonesByResultComparison = $this->type4CloneDetector->detect($type4SCCS);

        $type4Clones = array_merge($type4ClonesByConstructNormalization, $type4ClonesByResultComparison);

        $filteredType4Clones = $this->removeType4ClonesFullyCoveredByOtherClonesAlready($type4Clones, $type1Clones, $type2Clones, $type3Clones);

        return [
            SourceClone::TYPE_1 => $type1Clones,
            SourceClone::TYPE_2 => $type2Clones,
            SourceClone::TYPE_3 => $type3Clones,
            SourceClone::TYPE_4 => $filteredType4Clones,
        ];
    }

    /**
     * @param MethodSignatureGroup[] $methodSignatureGroups
     * @param SourceCloneCandidate[] $sccs
     * @param SourceClone[] $clones
     *
     * @return SourceCloneCandidate[]
     */
    private function removeSCCsFullyCoveredByCloneAndMethodSignatureGroup(array $methodSignatureGroups, array $sccs, array $clones): array
    {
        $sccsToRemove = [];
        foreach ($clones as $clone) {
            foreach ($sccs as $x => $type1SCC) {
                if ($clone->getMethodsCollection()->equals($type1SCC->getMethodsCollection())) {
                    foreach ($methodSignatureGroups as $methodSignatureGroup) {
                        if ($type1SCC->getMethodsCollection()->equals($methodSignatureGroup->getMethodsCollection())) {
                            $sccsToRemove[$x] = $type1SCC;
                        }
                    }
                }
            }
        }

        return array_diff_key($sccs, $sccsToRemove);
    }

    /**
     * @param MethodSignatureGroup[] $methodSignatureGroups
     * @param SourceClone[] $type1Clones
     * @param SourceClone[] $type2Clones
     * @param SourceClone[] $type3Clones
     *
     * @return MethodSignatureGroup[]
     */
    private function removeMethodSignatureGroupsFullyCoveredByClonesAlready(array $methodSignatureGroups, array $type1Clones, array $type2Clones, array $type3Clones): array
    {
        /** @var MethodSignatureGroup[] $filtered */
        $filtered = $this->removeItemsFullyCoveredByOtherClonesAlready($methodSignatureGroups, $type1Clones, $type2Clones, $type3Clones);

        return $filtered;
    }

    /**
     * @param SourceClone[] $type4Clones
     * @param SourceClone[] $type1Clones
     * @param SourceClone[] $type2Clones
     * @param SourceClone[] $type3Clones
     *
     * @return SourceClone[]
     */
    private function removeType4ClonesFullyCoveredByOtherClonesAlready(array $type4Clones, array $type1Clones, array $type2Clones, array $type3Clones): array
    {
        /** @var SourceClone[] $filtered */
        $filtered = $this->removeItemsFullyCoveredByOtherClonesAlready($type4Clones, $type1Clones, $type2Clones, $type3Clones);

        return $filtered;
    }

    /**
     * @param array<MethodSignatureGroup|SourceClone> $items
     * @param SourceClone[] $type1Clones
     * @param SourceClone[] $type2Clones
     * @param SourceClone[] $type3Clones
     *
     * @return array<MethodSignatureGroup|SourceClone>
     */
    private function removeItemsFullyCoveredByOtherClonesAlready(array $items, array $type1Clones, array $type2Clones, array $type3Clones): array
    {
        $itemsToRemove = [];
        foreach ($items as $i => $item) {
            foreach ($type1Clones as $clone) {
                if ($item->getMethodsCollection()->equals($clone->getMethodsCollection())) {
                    $itemsToRemove[$i] = $item;
                }
            }
            foreach ($type2Clones as $clone) {
                if ($item->getMethodsCollection()->equals($clone->getMethodsCollection())) {
                    $itemsToRemove[$i] = $item;
                }
            }
            foreach ($type3Clones as $clone) {
                if ($item->getMethodsCollection()->equals($clone->getMethodsCollection())) {
                    $itemsToRemove[$i] = $item;
                }
            }
        }

        return array_diff_key($items, $itemsToRemove);
    }

    /**
     * @param iterable<MethodSignatureGroup> $filteredMethodSignatureGroups
     *
     * @return SourceClone[]
     *
     * @throws CollectionCannotBeEmpty
     * @throws FilesystemException
     * @throws MethodCannotBeModifiedToNonClassContext
     * @throws NoParamGeneratorFoundForParamRequest
     */
    private function detectType4ClonesByConstructNormalization(iterable $filteredMethodSignatureGroups, DetectClonesCommandOutput $output): array
    {
        $methodSignatureGroupsWithLanguageConstructNormalizedMethods = [];
        foreach ($filteredMethodSignatureGroups as $methodSignatureGroup) {
            $methods = $methodSignatureGroup->getMethodsCollection()->getAll();

            $languageConstructNormalizedMethods = [];

            foreach ($methods as $method) {
                $languageConstructNormalizedMethodCode = $this->constructNormalizeService->normalizeMethod(
                    $method->getContent()
                );

                $languageConstructNormalizedMethods[] = Method::create(
                    $method->getMethodSignature(),
                    $method->getName(),
                    $method->getFilepath(),
                    $method->getCodePositionRange(),
                    $languageConstructNormalizedMethodCode,
                );
            }

            $methodSignatureGroupsWithLanguageConstructNormalizedMethods[] = MethodSignatureGroup::create(
                $methodSignatureGroup->getMethodSignature(),
                MethodsCollection::create(...$languageConstructNormalizedMethods)
            );
        }

        $clones = $this->detectClones($methodSignatureGroupsWithLanguageConstructNormalizedMethods, $output, false);

        $allClonesTogether = array_merge(
            $clones[SourceClone::TYPE_1],
            $clones[SourceClone::TYPE_2],
            $clones[SourceClone::TYPE_3],
        );

        $type4Clones = [];
        foreach ($allClonesTogether as $clone) {
            $type4Clones[] = SourceClone::create(
                SourceClone::TYPE_4,
                $clone->getMethodsCollection()
            );
        }

        return $type4Clones;
    }
}
