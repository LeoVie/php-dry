<?php

namespace App\Report\Formatter;

use App\Report\Report;
use App\ServiceFactory\EnvironmentFactory;
use App\ServiceFactory\FileSystemLoaderFactory;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HtmlReportFormatter implements ReportFormatter
{
    public function __construct(
        private FileSystemLoaderFactory $fileSystemLoaderFactory,
        private EnvironmentFactory      $environmentFactory,
    )
    {
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function format(Report $report): string
    {
        $loader = $this->fileSystemLoaderFactory->create(__DIR__ . '/../../../templates/php-dry');
        $twig = $this->environmentFactory->create($loader, ['cache' => '/tmp/twig_compilation_cache']);

        $template = $twig->load('php-dry.html.twig');

        return $template->render(['output' => $report->getAll()]);
    }
}
