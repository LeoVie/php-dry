<?php

declare(strict_types=1);

namespace App\Report;

use App\Model\SourceClone\SourceClone;

class JsonReportBuilder
{
    /** @param array<SourceClone> $sourceClones */
    public function build(array $sourceClones): string
    {
        return \Safe\json_encode($sourceClones);
    }
}