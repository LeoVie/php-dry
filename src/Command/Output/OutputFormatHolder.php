<?php

declare(strict_types=1);

namespace App\Command\Output;

use App\Exception\OutputFormatNotFound;

class OutputFormatHolder
{
    /** @param iterable<OutputFormat> $outputFormats */
    public function __construct(private iterable $outputFormats)
    {
    }

    /** @throws OutputFormatNotFound */
    public function pickByName(string $name): OutputFormat
    {
        foreach ($this->outputFormats as $outputFormat) {
            if ($outputFormat->getName() === $name) {
                return $outputFormat;
            }
        }

        throw OutputFormatNotFound::create($name);
    }
}