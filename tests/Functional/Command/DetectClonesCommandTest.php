<?php

declare(strict_types=1);

namespace App\Tests\Functional\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class DetectClonesCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $command = $application->find('php-dry:check');
        $this->commandTester = new CommandTester($command);
    }

    public function testHumanOutput(): void
    {
        $testdataDir = __DIR__ . '/../../testdata/clone-detection-testdata/';

        $this->commandTester->execute([
            'directory' => $testdataDir,
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertCommandFailed();
        self::assertStringContainsString('Found 13 files', $output);
        self::assertStringContainsString('Found 13 methods', $output);
        self::assertStringContainsString('Detecting type 1 clones', $output);
        self::assertStringContainsString('Detecting type 2 clones', $output);
        self::assertStringContainsString('Detecting type 3 clones', $output);
        self::assertStringContainsString('Detecting type 4 by running clones', $output);
    }

    private function assertCommandFailed(): void
    {
        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testDetectsExpectedClones(): void
    {
        $testdataDir = __DIR__ . '/../../testdata/clone-detection-testdata/';

        $this->commandTester->execute([
            'directory' => $testdataDir,
            '--outputFormat' => 'json'
        ]);

        $output = $this->commandTester->getDisplay();

        $expectedOutput = file_get_contents(__DIR__ . '/output.json');
        $expectedOutput = str_replace('%testdata_dir%', $testdataDir, $expectedOutput);

        $this->assertCommandFailed();
        self::assertJsonStringEqualsJsonString($expectedOutput, $output);
    }
}