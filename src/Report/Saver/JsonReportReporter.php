<?php

namespace App\Report\Saver;

use App\Configuration\Configuration;
use App\Configuration\ReportConfiguration\Json;
use Safe\Exceptions\FilesystemException;

class JsonReportReporter implements ReportReporter
{
    /**
     * @throws FilesystemException
     */
    public function report(string $report, Configuration $configuration): void
    {
        /** @var Json $jsonConfiguration */
        $jsonConfiguration = $configuration->getReportConfiguration()->getJson();

        \Safe\file_put_contents($jsonConfiguration->getFilepath(), $report);
    }
}