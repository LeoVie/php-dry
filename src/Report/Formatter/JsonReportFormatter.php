<?php

declare(strict_types=1);

namespace App\Report\Formatter;

use App\Report\Report;
use Safe\Exceptions\JsonException;

class JsonReportFormatter implements ReportFormatter
{
    /**
     * @throws JsonException
     */
    public function format(Report $report): string
    {
        return \Safe\json_encode($report->getAll());
    }
}
