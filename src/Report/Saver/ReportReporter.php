<?php

namespace App\Report\Saver;

interface ReportReporter
{
    public function report(string $report): void;
}