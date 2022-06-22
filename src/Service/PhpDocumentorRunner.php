<?php

namespace App\Service;

use App\Configuration\Configuration;
use App\Exception\PhpDocumentorFailed;

class PhpDocumentorRunner
{
    private const TEMPLATE_PATH = __DIR__ . '/../../config/phpDocumentor/template';

    public function run(string $directory): void
    {
        $configuration = Configuration::instance();

        $command = sprintf(
            '%s run --directory %s --target %s --template %s --force 2>&1',
            $configuration->getPhpDocumentorExecutablePath(),
            $directory,
            $configuration->getPhpDocumentorReportPath(),
            self::TEMPLATE_PATH
        );

        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            throw PhpDocumentorFailed::create(join("\n", $output));
        }
    }
}