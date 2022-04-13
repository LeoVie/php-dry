<?php

namespace App\Report\Saver;

use App\Configuration\Configuration;

interface ReportReporter
{
    public function report(string $report, Configuration $configuration): void;
}