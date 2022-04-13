<?php

namespace App\Report\Formatter;

use App\Report\Report;

interface ReportFormatter
{
    public function format(Report $report): string;
}