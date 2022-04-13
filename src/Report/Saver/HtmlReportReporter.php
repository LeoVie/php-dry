<?php

namespace App\Report\Saver;

use App\Configuration\Configuration;
use App\Configuration\ReportConfiguration\Html;
use Safe\Exceptions\FilesystemException;

class HtmlReportReporter implements ReportReporter
{
    /**
     * @throws FilesystemException
     */
    public function report(string $report, Configuration $configuration): void
    {
        /** @var Html $htmlConfiguration */
        $htmlConfiguration = $configuration->getReportConfiguration()->getHtml();

        $htmlDirectory = rtrim($htmlConfiguration->getDirectory()) . '/php-dry_html-report/';
        $htmlFilepath = $htmlDirectory . 'php-dry.html';

        if (!file_exists($htmlDirectory)) {
            \Safe\mkdir($htmlDirectory);
        }
        if (!file_exists($htmlDirectory . '/resources/')) {
            \Safe\mkdir($htmlDirectory . '/resources/');
        }
        if (!file_exists($htmlDirectory . '/resources/icons/')) {
            \Safe\mkdir($htmlDirectory . '/resources/icons/');
        }

        \Safe\copy(__DIR__ . '/../../../resources/icons/php-dry.svg', $htmlDirectory . '/resources/icons/php-dry.svg');

        \Safe\file_put_contents($htmlFilepath, $report);
    }
}