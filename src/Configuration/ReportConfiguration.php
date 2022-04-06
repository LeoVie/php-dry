<?php

namespace App\Configuration;

use App\Configuration\ReportConfiguration\Cli;
use App\Configuration\ReportConfiguration\Html;
use App\Configuration\ReportConfiguration\Json;

class ReportConfiguration
{
    private function __construct(
        private ?Cli  $cli,
        private ?Html $html,
        private ?Json $json
    )
    {
    }

    public static function create(
        ?Cli  $cli,
        ?Html $html,
        ?Json $json
    ): self
    {
        return new self($cli, $html, $json);
    }

    public function getCli(): ?Cli
    {
        return $this->cli;
    }

    public function getHtml(): ?Html
    {
        return $this->html;
    }

    public function getJson(): ?Json
    {
        return $this->json;
    }
}