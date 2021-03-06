<?php

namespace App;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;

class Application extends BaseApplication
{
    /** @param iterable<Command> $commands */
    public function __construct(iterable $commands)
    {
        $commands = $commands instanceof \Traversable ? \iterator_to_array($commands) : $commands;

        foreach ($commands as $command) {
            $this->add($command);
        }

        parent::__construct('php-dry', \Safe\file_get_contents(__DIR__ . '/../VERSION'));
    }
}
