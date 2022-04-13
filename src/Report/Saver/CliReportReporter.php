<?php

namespace App\Report\Saver;

use App\Command\Output\DetectClonesCommandOutput;
use App\Configuration\Configuration;

class CliReportReporter implements ReportReporter
{
    public function __construct(private DetectClonesCommandOutput $detectClonesCommandOutput)
    {
    }

    public function report(string $report, Configuration $configuration): void
    {
        $this->detectClonesCommandOutput->single($report);
    }
}