<?php

declare(strict_types=1);

namespace App\Report\Formatter;

use App\Report\Report;

class CliReportFormatter implements ReportFormatter
{
    private string $cliReport = '';

    public function format(Report $report): string
    {
        foreach ($report->getAll() as $type => $sourceCloneMethodScoresMappings) {
            $this->addLine($type);
            $this->addLine('------');
            $this->addLine('');

            foreach ($sourceCloneMethodScoresMappings as $sourceCloneMethodScoresMapping) {
                $clone = $sourceCloneMethodScoresMapping['sourceClone'];

                foreach ($clone['methods'] as $method) {
                    $m = $method['method'];
                    $this->addLine(' * ' . $m['filepath']
                        . ': ' . $m['name']
                        . ' (' . $m['codePositionRange']['start']['line']
                        . ' (position ' . $m['codePositionRange']['start']['filePos'] . ')'
                        . ' - ' . $m['codePositionRange']['end']['line']
                        . ' (position ' . $m['codePositionRange']['end']['filePos'] . ')'
                        . ' (' . $m['codePositionRange']['countOfLines'] . ' lines)'
                        . ')');
                }
            }
        }

        return $this->popReport();
    }

    private function addLine(string $line): void
    {
        $this->cliReport .= $line . "\n";
    }

    private function popReport(): string
    {
        $report = $this->cliReport;
        $this->cliReport = '';

        return $report;
    }
}
