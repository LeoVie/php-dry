<?php

declare(strict_types=1);

namespace App\Service;

use App\Collection\MethodsCollection;
use App\Command\Output\DetectClonesCommandOutput;
use App\Exception\OtherNodeTypeExpected;
use App\File\FindFiles;
use App\Grouper\MethodsBySignatureGrouper;
use App\Grouper\MethodTokenSequencesByTokenSequencesGrouper;
use App\Model\Method\MethodTokenSequence;
use App\Model\SourceClone\SourceClone;
use App\Model\TokenSequenceRepresentative;
use App\Tokenize\TokenSequenceFactory;
use Safe\Exceptions\FilesystemException;

class DetectClonesService
{
    public function __construct(
        private FindFiles                                   $findFiles,
        private MethodTokenSequencesByTokenSequencesGrouper $methodTokenSequencesByTokenSequencesGrouper,
        private FindMethodsInPathsService                   $findMethodsInPathsService,
        private MethodsBySignatureGrouper                   $methodsBySignatureGrouper,
        private TokenSequenceFactory                        $tokenSequenceFactory,
    )
    {
    }

    /**
     * @return SourceClone[][]
     *
     * @throws OtherNodeTypeExpected
     * @throws FilesystemException
     */
    public function detectInDirectory(string $directory, int $countOfParamSets, DetectClonesCommandOutput $output): array
    {
        $filePaths = $this->findFiles->findPhpFilesInPath($directory);

        $output->single(\Safe\sprintf('Found %s files.', count($filePaths)));

        // TODO: use stopwatch
        $now = time();

        $methods = $this->findMethodsInPathsService->find($filePaths);

        $output->single(\Safe\sprintf('Found %s methods in %s s.', count($methods), time() - $now));


        $methodsGroupedBySignatures = $this->methodsBySignatureGrouper->group($methods);
        $tokenSequenceRepresentatives = $this->createTokenSequenceRepresentatives($methodsGroupedBySignatures);

        return [
            SourceClone::TYPE_1 => $this->detectType1Clones($tokenSequenceRepresentatives),
        ];
    }

    /**
     * @param TokenSequenceRepresentative[] $tokenSequenceRepresentatives
     *
     * @return SourceClone[]
     */
    private function detectType1Clones(array $tokenSequenceRepresentatives): array
    {
        return array_map(
            fn(TokenSequenceRepresentative $tsr): SourceClone => SourceClone::createType1($tsr->getMethodsCollection()),
            array_filter(
                $tokenSequenceRepresentatives,
                fn(TokenSequenceRepresentative $sc): bool => $sc->getMethodsCollection()->count() > 1
            )
        );
    }

    /**
     * @param MethodsCollection[] $methodsGroupedBySignatures
     *
     * @return TokenSequenceRepresentative[]
     */
    private function createTokenSequenceRepresentatives(array $methodsGroupedBySignatures): array
    {
        $tokenSequenceRepresentatives = [];
        foreach ($methodsGroupedBySignatures as $methodsCollection) {
            $methodTokenSequences = [];
            foreach ($methodsCollection->getAll() as $method) {
                $methodTokenSequences[] = MethodTokenSequence::create($method,
                    $this->tokenSequenceFactory->createNormalizedLevel1('<?php ' . $method->getContent())
                );
            }

            $groupedByTokenSequences = $this->methodTokenSequencesByTokenSequencesGrouper->group($methodTokenSequences);

            foreach ($groupedByTokenSequences as $group) {
                $methodsCollection = MethodsCollection::empty();
                foreach ($group as $methodTokenSequence) {
                    $methodsCollection->add($methodTokenSequence->getMethod());
                }

                $tokenSequenceRepresentatives[] = TokenSequenceRepresentative::create($group[0]->getTokenSequence(), $methodsCollection);
            }
        }

        return $tokenSequenceRepresentatives;
    }
}