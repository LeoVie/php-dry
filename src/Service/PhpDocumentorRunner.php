<?php

namespace App\Service;

use App\Configuration\Configuration;
use App\Exception\PhpDocumentorFailed;

class PhpDocumentorRunner
{
    private const TEMPLATE_PATH = __DIR__ . '/../../config/phpDocumentor/template';
    private const MINIMAL_TEMPLATE_PATH = __DIR__ . '/../../config/phpDocumentor/minimal-template';

    public function run(string $directory): void
    {
       $this->runWithTemplate($directory, self::TEMPLATE_PATH);
    }

    public function runMinimal(string $directory): void
    {
        $this->runWithTemplate($directory, self::MINIMAL_TEMPLATE_PATH);
    }

    private function runWithTemplate(string $directory, string $templatePath): void
    {
        $configuration = Configuration::instance();

        $command = sprintf(
            '%s run --directory %s --target %s --template %s --force 2>&1',
            $configuration->getPhpDocumentorExecutablePath(),
            $directory,
            $configuration->getPhpDocumentorReportPath(),
            $templatePath
        );

        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            throw PhpDocumentorFailed::create(join("\n", $output));
        }
    }
}