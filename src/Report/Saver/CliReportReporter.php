<?php

namespace App\Report\Saver;

use App\Command\Output\DetectClonesCommandOutput;

class CliReportReporter implements ReportReporter
{
    public function __construct(private DetectClonesCommandOutput $detectClonesCommandOutput)
    {
    }

    public function report(string $report): void
    {
        $this->detectClonesCommandOutput->single($report);
    }
}