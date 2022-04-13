<?php

declare(strict_types=1);

namespace App\Configuration;

class Configuration
{
    private string $directory;

    private function __construct(
        private bool                $silent,
        private int                 $minTokenLength,
        private int                 $minSimilarTokensPercentage,
        private bool                $enableLcsAlgorithm,
        private int                 $countOfParamSets,
        private bool                $enableConstructNormalization,
        private string              $phpDocumentorReportPath,
        private string              $phpDocumentorExecutablePath,
        private ReportConfiguration $reportConfiguration
    )
    {
    }

    public static function create(
        bool                $silent,
        int                 $minTokenLength,
        int                 $minSimilarTokensPercentage,
        bool                $enableLcsAlgorithm,
        int                 $countOfParamSets,
        bool                $enableConstructNormalization,
        string              $phpDocumentorReportPath,
        string              $phpDocumentorExecutablePath,
        ReportConfiguration $reportConfiguration
    ): self
    {
        return new self(
            $silent,
            $minTokenLength,
            $minSimilarTokensPercentage,
            $enableLcsAlgorithm,
            $countOfParamSets,
            $enableConstructNormalization,
            $phpDocumentorReportPath,
            $phpDocumentorExecutablePath,
            $reportConfiguration
        );
    }

    public function setDirectory(string $directory): void
    {
        $this->directory = $directory;
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

    public function getReportConfiguration(): ReportConfiguration
    {
        return $this->reportConfiguration;
    }
}
