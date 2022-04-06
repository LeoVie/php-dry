<?php

declare(strict_types=1);

namespace App\Report;

use App\Model\SourceClone\SourceClone;

class CommandLineReportBuilder
{
    private string $report = '';

    public function build(string $jsonReport): string
    {
        /** @var array{0: array{'type': string, 'methods': array{0: array{'filepath': string, 'name': string, 'codePositionRange': array{'start': array{'line': int, 'filePos': int }, 'end': array{'line': int, 'filePos': int }, 'countOfLines': int }}}}} $clones */
        $clones = \Safe\json_decode($jsonReport, true);
        foreach ($clones as $clone) {
            $this->addLine($clone['type']);
            $this->addLine('------');
            $this->addLine('');
            foreach ($clone['methods'] as $method) {
                $this->addLine(' * ' . $method['filepath']
                    . ': ' . $method['name']
                    . ' (' . $method['codePositionRange']['start']['line']
                    . ' (position ' . $method['codePositionRange']['start']['filePos'] . ')'
                    . ' - ' . $method['codePositionRange']['end']['line']
                    . ' (position ' . $method['codePositionRange']['end']['filePos'] . ')'
                    . ' (' . $method['codePositionRange']['countOfLines'] . ' lines)'
                    . ')');
            }
            $this->addLine('');
        }

        return $this->popReport();
    }

    private function addLine(string $line): self
    {
        $this->report .= $line . "\n";

        return $this;
    }

    private function popReport(): string
    {
        $report = $this->report;
        $this->report = '';

        return $report;
    }
}
