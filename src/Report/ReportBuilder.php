<?php

namespace App\Report;

use App\Configuration\Configuration;
use App\Model\SourceClone\SourceClone;
use App\Model\SourceCloneMethodScoresMapping;
use App\Report\Converter\SourceCloneMethodScoreMappingsToArrayConverter;

class ReportBuilder
{
    public function __construct(private SourceCloneMethodScoreMappingsToArrayConverter $sourceCloneMethodScoreMappingsToArrayConverter)
    {
    }

    /** @param array<SourceCloneMethodScoresMapping> $sourceCloneMethodScoresMappings */
    public function createReport(array $sourceCloneMethodScoresMappings, Configuration $configuration): Report
    {
        $sortedSourceCloneMethodScoreMappings = [
            SourceClone::TYPE_1 => [],
            SourceClone::TYPE_2 => [],
            SourceClone::TYPE_3 => [],
            SourceClone::TYPE_4 => [],
        ];

        foreach ($sourceCloneMethodScoresMappings as $sourceCloneMethodScoresMapping) {
            $methodScoresMappings = $sourceCloneMethodScoresMapping->getMethodScoresMappings();

            $sourceCloneMethodScoresMappingWithRelativePath = SourceCloneMethodScoresMapping::create(
                $sourceCloneMethodScoresMapping->getSourceClone(),
                $methodScoresMappings
            );

            $cloneType = $sourceCloneMethodScoresMappingWithRelativePath->getSourceClone()->getType();
            $sortedSourceCloneMethodScoreMappings[$cloneType][] = $sourceCloneMethodScoresMappingWithRelativePath;
        }

        return Report::create(
            $this->sourceCloneMethodScoreMappingsToArrayConverter
                ->sourceCloneMethodScoresMappingToArray($sortedSourceCloneMethodScoreMappings[SourceClone::TYPE_1], $configuration),
            $this->sourceCloneMethodScoreMappingsToArrayConverter
                ->sourceCloneMethodScoresMappingToArray($sortedSourceCloneMethodScoreMappings[SourceClone::TYPE_2], $configuration),
            $this->sourceCloneMethodScoreMappingsToArrayConverter
                ->sourceCloneMethodScoresMappingToArray($sortedSourceCloneMethodScoreMappings[SourceClone::TYPE_3], $configuration),
            $this->sourceCloneMethodScoreMappingsToArrayConverter
                ->sourceCloneMethodScoresMappingToArray($sortedSourceCloneMethodScoreMappings[SourceClone::TYPE_4], $configuration),
        );
    }
}