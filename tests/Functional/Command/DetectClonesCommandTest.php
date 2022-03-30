<?php

declare(strict_types=1);

namespace App\Tests\Functional\Command;

use App\Command\DetectClonesCommand;
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
        $command = $application->find(DetectClonesCommand::NAME);
        $this->commandTester = new CommandTester($command);
    }

    public function testHumanOutput(): void
    {
        $testdataDir = __DIR__ . '/../../testdata/clone-detection-testdata/';

        $this->commandTester->execute([
            DetectClonesCommand::ARGUMENT_DIRECTORY => $testdataDir,
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertCommandFailed();
        self::assertStringContainsString('Found 13 files', $output);
        self::assertStringContainsString('Found 13 methods', $output);
        self::assertStringContainsString('Detecting type 1 clones', $output);
        self::assertStringContainsString('Detecting type 2 clones', $output);
        self::assertStringContainsString('Detecting type 3 clones', $output);
        self::assertStringContainsString('Detecting type 4 by running clones', $output);

        $type1ClonesOutput = "TYPE_1
        ------
        
         * ${testdataDir}01_A.php: foo (5 (position 50) - 13 (position 231) (8 lines))
         * ${testdataDir}03_A_Exact_Copy.php: foo (5 (position 61) - 13 (position 242) (8 lines))
         * ${testdataDir}04_A_Additional_Whitespaces.php: foo (5 (position 73) - 14 (position 276) (9 lines))
         * ${testdataDir}05_A_Additional_Comments.php: foo (5 (position 70) - 20 (position 440) (15 lines))
         * ${testdataDir}06_A_Changed_Layout.php: foo (5 (position 65) - 9 (position 210) (4 lines))";

        self::assertStringContainsString(
            $this->normalizeCommandLineOutput($type1ClonesOutput),
            $this->normalizeCommandLineOutput($output)
        );

        $type2ClonesOutput = "TYPE_2
        ------
        
         * ${testdataDir}01_A.php: foo (5 (position 50) - 13 (position 231) (8 lines))
         * ${testdataDir}03_A_Exact_Copy.php: foo (5 (position 61) - 13 (position 242) (8 lines))
         * ${testdataDir}04_A_Additional_Whitespaces.php: foo (5 (position 73) - 14 (position 276) (9 lines))
         * ${testdataDir}05_A_Additional_Comments.php: foo (5 (position 70) - 20 (position 440) (15 lines))
         * ${testdataDir}06_A_Changed_Layout.php: foo (5 (position 65) - 9 (position 210) (4 lines))
         * ${testdataDir}07_A_Changed_Variable_Names.php: foo (5 (position 73) - 13 (position 268) (8 lines))
         * ${testdataDir}08_A_Changed_Method_Names.php: bar (5 (position 71) - 13 (position 252) (8 lines))
         * ${testdataDir}09_A_Changed_Literals.php: foo (5 (position 67) - 13 (position 248) (8 lines))";
        self::assertStringContainsString(
            $this->normalizeCommandLineOutput($type2ClonesOutput),
            $this->normalizeCommandLineOutput($output)
        );

        $type3ClonesOutput = "TYPE_3
        ------
        
         * ${testdataDir}01_A.php: foo (5 (position 50) - 13 (position 231) (8 lines))
         * ${testdataDir}03_A_Exact_Copy.php: foo (5 (position 61) - 13 (position 242) (8 lines))
         * ${testdataDir}04_A_Additional_Whitespaces.php: foo (5 (position 73) - 14 (position 276) (9 lines))
         * ${testdataDir}05_A_Additional_Comments.php: foo (5 (position 70) - 20 (position 440) (15 lines))
         * ${testdataDir}06_A_Changed_Layout.php: foo (5 (position 65) - 9 (position 210) (4 lines))
         * ${testdataDir}07_A_Changed_Variable_Names.php: foo (5 (position 73) - 13 (position 268) (8 lines))
         * ${testdataDir}08_A_Changed_Method_Names.php: bar (5 (position 71) - 13 (position 252) (8 lines))
         * ${testdataDir}09_A_Changed_Literals.php: foo (5 (position 67) - 13 (position 248) (8 lines))
         * ${testdataDir}11_A_Removed_Statements.php: foo (5 (position 69) - 12 (position 231) (7 lines))
         * ${testdataDir}12_A_Changed_Statement_Order.php: foo (5 (position 74) - 13 (position 255) (8 lines))";
        self::assertStringContainsString(
            $this->normalizeCommandLineOutput($type3ClonesOutput),
            $this->normalizeCommandLineOutput($output)
        );

        $type4ClonesOutput = "TYPE_4
        ------
        
         * ${testdataDir}01_A.php: foo (5 (position 50) - 13 (position 231) (8 lines))
         * ${testdataDir}03_A_Exact_Copy.php: foo (5 (position 61) - 13 (position 242) (8 lines))
         * ${testdataDir}04_A_Additional_Whitespaces.php: foo (5 (position 73) - 14 (position 276) (9 lines))
         * ${testdataDir}05_A_Additional_Comments.php: foo (5 (position 70) - 20 (position 440) (15 lines))
         * ${testdataDir}06_A_Changed_Layout.php: foo (5 (position 65) - 9 (position 210) (4 lines))
         * ${testdataDir}07_A_Changed_Variable_Names.php: foo (5 (position 73) - 13 (position 268) (8 lines))
         * ${testdataDir}08_A_Changed_Method_Names.php: bar (5 (position 71) - 13 (position 252) (8 lines))
         * ${testdataDir}09_A_Changed_Literals.php: foo (5 (position 67) - 13 (position 248) (8 lines))
         * ${testdataDir}10_A_Additional_Statements.php: foo (5 (position 72) - 14 (position 290) (9 lines))
         * ${testdataDir}11_A_Removed_Statements.php: foo (5 (position 69) - 12 (position 231) (7 lines))
         * ${testdataDir}12_A_Changed_Statement_Order.php: foo (5 (position 74) - 13 (position 255) (8 lines))
         * ${testdataDir}13_A_Changed_Syntax.php: foo (5 (position 65) - 11 (position 210) (6 lines))";
        self::assertStringContainsString(
            $this->normalizeCommandLineOutput($type4ClonesOutput),
            $this->normalizeCommandLineOutput($output)
        );
    }

    private function normalizeCommandLineOutput(string $commandLineOutput): string
    {
        return join('', array_filter(
            array_map(
                fn (string $s): string => trim($s),
                explode("\n", str_replace("\r", '', $commandLineOutput))
            ),
            fn (string $line): bool => $line !== ''
        ));
    }

    private function assertCommandFailed(): void
    {
        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testDetectsExpectedClones(): void
    {
        $testdataDir = __DIR__ . '/../../testdata/clone-detection-testdata/';
        $reportsDir = __DIR__ . '/../../generated/reports';
        $reportPath = $reportsDir . '/php-dry.json';

        if (file_exists($reportPath)) {
            unlink($reportPath);
        }

        self::assertFileDoesNotExist($reportPath);

        $this->commandTester->execute([
            DetectClonesCommand::ARGUMENT_DIRECTORY => $testdataDir,
            '--' . DetectClonesCommand::OPTION_REPORT_FORMAT => 'json',
            '--' . DetectClonesCommand::OPTION_REPORTS_DIRECTORY => __DIR__ . '/../../generated/reports',
        ]);

        $expectedJson = str_replace(
            '%testdata_dir%',
            $testdataDir,
            \Safe\file_get_contents(__DIR__ . '/output.json')
        );

        $this->assertCommandFailed();

        self::assertFileExists($reportPath);

        $actualJson = \Safe\file_get_contents($reportPath);
        self::assertJsonStringEqualsJsonString($expectedJson, $actualJson);
    }
}
