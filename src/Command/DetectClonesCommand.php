<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Output\DetectClonesCommandOutput;
use App\Command\Output\Helper\VerboseOutputHelper;
use App\Configuration\Configuration;
use App\Configuration\ConfigurationFactory;
use App\Exception\CollectionCannotBeEmpty;
use App\Exception\PhpDocumentorFailed;
use App\Exception\SubsequenceUtilNotFound;
use App\Report\Reporter;
use App\Service\DetectClonesService;
use App\Service\FindMethodsInPathsService;
use App\Service\FindConstructableClasses;
use App\Service\IgnoreClonesService;
use App\ServiceFactory\StopwatchFactory;
use LeoVie\PhpMethodModifier\Exception\MethodCannotBeModifiedToNonClassContext;
use LeoVie\PhpParamGenerator\Exception\NoParamGeneratorFoundForParamRequest;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\JsonException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class DetectClonesCommand extends Command
{
    public const NAME = 'php-dry:check';
    public const OPTION_CONFIG = 'config';
    protected static $defaultName = self::NAME;

    public function __construct(
        private ConfigurationFactory      $configurationFactory,
        private DetectClonesService       $detectClonesService,
        private IgnoreClonesService       $ignoreClonesService,
        private DetectClonesCommandOutput $detectClonesCommandOutput,
        private Reporter                  $reporter,
        private FindMethodsInPathsService $findMethodsInPathsService,
        private FindConstructableClasses  $findConstructableClasses,
    )
    {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this->addOption(
            self::OPTION_CONFIG,
            'c',
            InputOption::VALUE_REQUIRED,
            'Path to php-dry.xml',
        );
    }

    /**
     * @throws CollectionCannotBeEmpty
     * @throws FilesystemException
     * @throws JsonException
     * @throws LoaderError
     * @throws MethodCannotBeModifiedToNonClassContext
     * @throws NoParamGeneratorFoundForParamRequest
     * @throws RuntimeError
     * @throws SubsequenceUtilNotFound
     * @throws SyntaxError
     * @throws PhpDocumentorFailed
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = StopwatchFactory::create();
        $stopwatch->start('detect-clones');

        $commandOutput = $this->detectClonesCommandOutput
            ->setOutputHelper(VerboseOutputHelper::create($input, $output))
            ->setStopwatch($stopwatch);

        $this->createConfiguration($input);
        $configuration = Configuration::instance();

        $projectClasses = [];
        foreach ($configuration->getDirectories() as $directory) {
            $projectClasses = array_merge(
                $projectClasses,
                $this->findConstructableClasses->findAll($directory)
            );
        }

        $vendorClasses = [];
        if ($configuration->getVendorPath() !== '') {
            $vendorClasses = $this->findConstructableClasses->findAll($configuration->getVendorPath());
        }

        $constructableClasses = array_merge($projectClasses, $vendorClasses);

        $methods = [];
        foreach ($configuration->getDirectories() as $directory) {
            $methods = array_merge($methods, $this->findMethodsInPathsService->findAll($directory));
        }

        $detectedClones = $this->detectClonesService->detectInMethods($commandOutput, $methods, $constructableClasses);
        $relevantClones = $this->ignoreClonesService->extractNonIgnoredClones($detectedClones);

        if (empty($relevantClones)) {
            $commandOutput->noClonesFound()->stopTime();

            return Command::SUCCESS;
        }

        $commandOutput->newLine(2);
        $this->reporter->report($relevantClones);

        $commandOutput->stopTime();

        return Command::FAILURE;
    }

    private function createConfiguration(InputInterface $input): void
    {
        $this->configurationFactory->createConfigurationFromXmlFile(
            $this->getStringOption($input, self::OPTION_CONFIG)
        );
    }

    private function getStringOption(InputInterface $input, string $name): string
    {
        /** @var string $value */
        $value = $input->getOption($name);

        return $value;
    }
}
