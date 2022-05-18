<?php

namespace App\Report;

use App\Model\SourceClone\SourceClone;
use App\Report\Converter\SourceClonesToArrayConverter;

class ReportBuilder
{
    public function __construct(private SourceClonesToArrayConverter $sourceClonesToArrayConverter)
    {
    }

    /** @param array<SourceClone> $clones */
    public function createReport(array $clones): Report
    {
        $sortedClones = [
            SourceClone::TYPE_1 => [],
            SourceClone::TYPE_2 => [],
            SourceClone::TYPE_3 => [],
            SourceClone::TYPE_4 => [],
        ];

        foreach ($clones as $clone) {
            $cloneType = $clone->getType();
            $sortedClones[$cloneType][] = $clone;
        }

        return Report::create(
            $this->sourceClonesToArrayConverter
                ->sourceClonesToArray($sortedClones[SourceClone::TYPE_1]),
            $this->sourceClonesToArrayConverter
                ->sourceClonesToArray($sortedClones[SourceClone::TYPE_2]),
            $this->sourceClonesToArrayConverter
                ->sourceClonesToArray($sortedClones[SourceClone::TYPE_3]),
            $this->sourceClonesToArrayConverter
                ->sourceClonesToArray($sortedClones[SourceClone::TYPE_4]),
        );
    }
}