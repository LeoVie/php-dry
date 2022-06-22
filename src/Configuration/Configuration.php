<?php

declare(strict_types=1);

namespace App\Configuration;

class Configuration
{
    private static Configuration $instance;

    /** @param array<string> $directories */
    private function __construct(
        private array               $directories,
        private bool                $silent,
        private int                 $minTokenLength,
        private int                 $minSimilarTokensPercentage,
        private bool                $enableLcsAlgorithm,
        private int                 $countOfParamSets,
        private bool                $enableConstructNormalization,
        private string              $phpDocumentorReportPath,
        private string              $phpDocumentorExecutablePath,
        private string              $cachePath,
        private string              $bootstrapScriptPath,
        private string              $vendorPath,
        private ReportConfiguration $reportConfiguration
    )
    {
    }

    /** @param array<string> $directories */
    public static function create(
        array               $directories,
        bool                $silent,
        int                 $minTokenLength,
        int                 $minSimilarTokensPercentage,
        bool                $enableLcsAlgorithm,
        int                 $countOfParamSets,
        bool                $enableConstructNormalization,
        string              $phpDocumentorReportPath,
        string              $phpDocumentorExecutablePath,
        string              $cachePath,
        string              $bootstrapScriptPath,
        string              $vendorPath,
        ReportConfiguration $reportConfiguration
    ): self
    {
        self::setInstance(new self(
            $directories,
            $silent,
            $minTokenLength,
            $minSimilarTokensPercentage,
            $enableLcsAlgorithm,
            $countOfParamSets,
            $enableConstructNormalization,
            $phpDocumentorReportPath,
            $phpDocumentorExecutablePath,
            $cachePath,
            $bootstrapScriptPath,
            $vendorPath,
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

    /** @return array<string> */
    public function getDirectories(): array
    {
        return $this->directories;
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

    public function getBootstrapScriptPath(): string
    {
        return $this->bootstrapScriptPath;
    }

    public function getVendorPath(): string
    {
        return $this->vendorPath;
    }

    public function getReportConfiguration(): ReportConfiguration
    {
        return $this->reportConfiguration;
    }
}
