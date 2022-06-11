<?php

declare(strict_types=1);

namespace App\Configuration;

class Configuration
{
    private static Configuration $instance;

    private function __construct(
        private string              $directory,
        private bool                $silent,
        private int                 $minTokenLength,
        private int                 $minSimilarTokensPercentage,
        private bool                $enableLcsAlgorithm,
        private int                 $countOfParamSets,
        private bool                $enableConstructNormalization,
        private string              $phpDocumentorReportPath,
        private string              $phpDocumentorExecutablePath,
        private string              $cachePath,
        private ReportConfiguration $reportConfiguration
    )
    {
    }

    public static function create(
        string              $directory,
        bool                $silent,
        int                 $minTokenLength,
        int                 $minSimilarTokensPercentage,
        bool                $enableLcsAlgorithm,
        int                 $countOfParamSets,
        bool                $enableConstructNormalization,
        string              $phpDocumentorReportPath,
        string              $phpDocumentorExecutablePath,
        string              $cachePath,
        ReportConfiguration $reportConfiguration
    ): self
    {
        self::setInstance(new self(
            $directory,
            $silent,
            $minTokenLength,
            $minSimilarTokensPercentage,
            $enableLcsAlgorithm,
            $countOfParamSets,
            $enableConstructNormalization,
            $phpDocumentorReportPath,
            $phpDocumentorExecutablePath,
            $cachePath,
            $reportConfiguration
        ));

        return self::$instance;
    }

    public static function instance(): self
    {
        return self::$instance;
    }

    public static function setInstance(self $instance): void
    {
        self::$instance = $instance;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function isSilent(): bool
    {
        return $this->silent;
    }

    public function getMinTokenLength(): int
    {
        return $this->minTokenLength;
    }

    public function getMinSimilarTokensPercentage(): int
    {
        return $this->minSimilarTokensPercentage;
    }

    public function isEnableLcsAlgorithm(): bool
    {
        return $this->enableLcsAlgorithm;
    }

    public function getCountOfParamSets(): int
    {
        return $this->countOfParamSets;
    }

    public function getEnableConstructNormalization(): bool
    {
        return $this->enableConstructNormalization;
    }

    public function getPhpDocumentorReportPath(): string
    {
        return $this->phpDocumentorReportPath;
    }

    public function getPhpDocumentorExecutablePath(): string
    {
        return $this->phpDocumentorExecutablePath;
    }

    public function getCachePath(): string
    {
        return $this->cachePath;
    }

    public function getReportConfiguration(): ReportConfiguration
    {
        return $this->reportConfiguration;
    }
}
