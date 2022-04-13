<?php

namespace App\Configuration;

use App\Configuration\ReportConfiguration\Cli;
use App\Configuration\ReportConfiguration\Html;
use App\Configuration\ReportConfiguration\Json;
use App\ServiceFactory\CrawlerFactory;
use Symfony\Component\DomCrawler\Crawler;

class ConfigurationFactory
{
    public function createConfigurationFromXmlFile(string $configurationXmlFilepath): Configuration
    {
        $configurationXmlDirectory = dirname($configurationXmlFilepath);

        $crawler = CrawlerFactory::create(\Safe\file_get_contents($configurationXmlFilepath));

        $this->nodeExists($crawler, 'php-dry > report');

        return Configuration::create(
            $this->getAsBool($crawler, 'php-dry', 'silent', false),
            $this->getAsInt($crawler, 'php-dry', 'minTokenLength', 50),
            $this->getAsInt($crawler, 'php-dry', 'minSimilarTokensPercentage', 80),
            $this->getAsBool($crawler, 'php-dry', 'enableLcsAlgorithm', false),
            $this->getAsInt($crawler, 'php-dry', 'countOfParamSets', 10),
            $this->getAsBool($crawler, 'php-dry', 'enableConstructNormalization', false),
            $this->relativePathToAbsolutePath(
                $this->getAsString($crawler, 'php-dry', 'phpDocumentorReportPath', '/tmp/phpDocumentorReport'),
                $configurationXmlDirectory
            ),
            $this->relativePathToAbsolutePath(
                $this->getAsString($crawler, 'php-dry', 'phpDocumentorExecutablePath', 'tools/phpDocumentor.phar'),
                $configurationXmlDirectory
            ),
            ReportConfiguration::create(
                $this->nodeExists($crawler, 'php-dry > report > cli') ? Cli::create() : null,
                $this->nodeExists($crawler, 'php-dry > report > html')
                    ? Html::create(
                        $this->relativePathToAbsolutePath(
                            $this->getAsString($crawler, 'php-dry > report > html', 'directory', ''),
                            $configurationXmlDirectory
                        )
                )
                    : null,
                $this->nodeExists($crawler, 'php-dry > report > json')
                    ? Json::create(
                    $this->relativePathToAbsolutePath(
                        $this->getAsString($crawler, 'php-dry > report > json', 'filepath', ''),
                        $configurationXmlDirectory
                    )
                )
                    : null,
            )
        );
    }

    private function nodeExists(Crawler $crawler, string $path): bool
    {
        return $crawler->filter($path)->first()->count() > 0;
    }

    private function getAsString(Crawler $crawler, string $path, string $attribute, string $default): string
    {
        /** @var string $value */
        $value = $crawler->filter($path)->first()->attr($attribute) ?? $default;

        return $value;
    }

    private function getAsInt(Crawler $crawler, string $path, string $attribute, int $default): int
    {
        /** @var int $value */
        $value = $crawler->filter($path)->first()->attr($attribute) ?? $default;

        return $value;
    }

    private function getAsBool(Crawler $crawler, string $path, string $attribute, bool $default): bool
    {
        $rawValue = $crawler->filter($path)->first()->attr($attribute);

        if ($rawValue === null) {
            return $default;
        }

        /** @var bool $value */
        $value = $rawValue === 'true';

        return $value;
    }

    private function relativePathToAbsolutePath(string $path, string $configurationXmlDirectory): string
    {
        $isAbsolutePath = str_starts_with($path, '/');
        if ($isAbsolutePath) {
            return $path;
        }

        return $configurationXmlDirectory . '/' . $path;
    }
}