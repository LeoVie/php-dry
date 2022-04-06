<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use DOMNode;
use Symfony\Component\DomCrawler\Crawler;

final class CrawlerFactory
{
    /** @param DOMNode|\DOMNodeList|string|DOMNode[]|null $node */
    public static function create($node = null, string $uri = null, string $baseHref = null): Crawler
    {
        return new Crawler($node, $uri, $baseHref);
    }
}
