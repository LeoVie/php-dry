<?php

declare(strict_types=1);

namespace App\Report;

use App\Configuration\Configuration;
use App\Model\SourceClone\SourceClone;
use App\Report\Formatter\CliReportFormatter;
use App\Report\Formatter\HtmlReportFormatter;
use App\Report\Formatter\JsonReportFormatter;
use App\Report\Saver\CliReportReporter;
use App\Report\Saver\HtmlReportReporter;
use App\Report\Saver\JsonReportReporter;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\JsonException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Reporter
{
    public function __construct(
        private HtmlReportFormatter $htmlReportFormatter,
        private JsonReportFormatter $jsonReportFormatter,
        private CliReportFormatter  $cliReportFormatter,
        private HtmlReportReporter  $htmlReportReporter,
        private JsonReportReporter  $jsonReportReporter,
        private CliReportReporter   $cliReportReporter,
        private ReportBuilder       $reportBuilder,
    )
    {
    }

    /**
     * @param array<SourceClone> $clones
     *
     * @throws FilesystemException
     * @throws JsonException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function report(array $clones): void
    {
        $report = $this->reportBuilder->createReport($clones);

        $configuration = Configuration::instance();

        if ($configuration->getReportConfiguration()->getHtml()) {
            $htmlReport = $this->htmlReportFormatter->format($report);
            $this->htmlReportReporter->report($htmlReport);
        }

        if ($configuration->getReportConfiguration()->getJson()) {
            $jsonReport = $this->jsonReportFormatter->format($report);
            $this->jsonReportReporter->report($jsonReport);
        }

        if ($configuration->getReportConfiguration()->getCli()) {
            $cliReport = $this->cliReportFormatter->format($report);
            $this->cliReportReporter->report($cliReport);
        }
    }
}