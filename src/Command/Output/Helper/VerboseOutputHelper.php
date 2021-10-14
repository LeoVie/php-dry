<?php

declare(strict_types=1);

namespace App\Command\Output\Helper;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VerboseOutputHelper
{
    private const VERBOSITY_LEVEL = OutputInterface::VERBOSITY_VERBOSE;

    public static function create(InputInterface $input, OutputInterface $output): OutputHelper
    {
        return OutputHelper::create($input, $output, self::VERBOSITY_LEVEL);
    }
}