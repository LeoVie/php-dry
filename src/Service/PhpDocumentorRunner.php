<?php

namespace App\Service;

use App\Configuration\Configuration;
use App\Exception\PhpDocumentorFailed;

class PhpDocumentorRunner
{
    private const TEMPLATE_PATH = __DIR__ . '/../../config/phpDocumentor/template';

    public function run(Configuration $configuration): void
    {
        $command = \Safe\sprintf(
            '%s run --directory %s --target %s --template %s 2>&1',
            $configuration->getPhpDocumentorExecutablePath(),
            $configuration->getDirectory(),
            $configuration->getPhpDocumentorReportPath(),
            self::TEMPLATE_PATH
        );

        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            throw PhpDocumentorFailed::create(join("\n", $output));
        }
    }
}