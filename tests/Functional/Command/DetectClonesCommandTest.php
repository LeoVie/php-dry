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

        // TODO: delete after refactoring
        $type1Clones = "TYPE_1
------

 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/01_A.php: foo (5 (position 50) - 13 (position 231) (8 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/03_A_Exact_Copy.php: foo (5 (position 61) - 13 (position 242) (8 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/04_A_Additional_Whitespaces.php: foo (5 (position 73) - 14 (position 276) (9 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/05_A_Additional_Comments.php: foo (5 (position 70) - 20 (position 440) (15 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/06_A_Changed_Layout.php: foo (5 (position 65) - 9 (position 210) (4 lines))";
        self::assertStringContainsString(str_replace(["\n", "\r"], '', $type1Clones), str_replace(["\n", "\r"], '', $output));

        $type2Clones = "TYPE_2
------

 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/01_A.php: foo (5 (position 50) - 13 (position 231) (8 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/03_A_Exact_Copy.php: foo (5 (position 61) - 13 (position 242) (8 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/04_A_Additional_Whitespaces.php: foo (5 (position 73) - 14 (position 276) (9 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/05_A_Additional_Comments.php: foo (5 (position 70) - 20 (position 440) (15 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/06_A_Changed_Layout.php: foo (5 (position 65) - 9 (position 210) (4 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/07_A_Changed_Variable_Names.php: foo (5 (position 73) - 13 (position 268) (8 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/08_A_Changed_Method_Names.php: bar (5 (position 71) - 13 (position 252) (8 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/09_A_Changed_Literals.php: foo (5 (position 67) - 13 (position 248) (8 lines))";
        self::assertStringContainsString(str_replace(["\n", "\r"], '', $type2Clones), str_replace(["\n", "\r"], '', $output));

        $type3Clones = "TYPE_3
------

 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/01_A.php: foo (5 (position 50) - 13 (position 231) (8 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/03_A_Exact_Copy.php: foo (5 (position 61) - 13 (position 242) (8 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/04_A_Additional_Whitespaces.php: foo (5 (position 73) - 14 (position 276) (9 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/05_A_Additional_Comments.php: foo (5 (position 70) - 20 (position 440) (15 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/06_A_Changed_Layout.php: foo (5 (position 65) - 9 (position 210) (4 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/07_A_Changed_Variable_Names.php: foo (5 (position 73) - 13 (position 268) (8 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/08_A_Changed_Method_Names.php: bar (5 (position 71) - 13 (position 252) (8 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/09_A_Changed_Literals.php: foo (5 (position 67) - 13 (position 248) (8 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/11_A_Removed_Statements.php: foo (5 (position 69) - 12 (position 231) (7 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/12_A_Changed_Statement_Order.php: foo (5 (position 74) - 13 (position 255) (8 lines))";
        self::assertStringContainsString(str_replace(["\n", "\r"], '', $type3Clones), str_replace(["\n", "\r"], '', $output));

        $type4Clones = "TYPE_4
------

 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/01_A.php: foo (5 (position 50) - 13 (position 231) (8 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/03_A_Exact_Copy.php: foo (5 (position 61) - 13 (position 242) (8 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/04_A_Additional_Whitespaces.php: foo (5 (position 73) - 14 (position 276) (9 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/05_A_Additional_Comments.php: foo (5 (position 70) - 20 (position 440) (15 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/06_A_Changed_Layout.php: foo (5 (position 65) - 9 (position 210) (4 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/07_A_Changed_Variable_Names.php: foo (5 (position 73) - 13 (position 268) (8 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/08_A_Changed_Method_Names.php: bar (5 (position 71) - 13 (position 252) (8 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/09_A_Changed_Literals.php: foo (5 (position 67) - 13 (position 248) (8 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/10_A_Additional_Statements.php: foo (5 (position 72) - 14 (position 290) (9 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/11_A_Removed_Statements.php: foo (5 (position 69) - 12 (position 231) (7 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/12_A_Changed_Statement_Order.php: foo (5 (position 74) - 13 (position 255) (8 lines))
 * /home/ubuntu/development/php-dry/tests/Functional/Command/../../testdata/clone-detection-testdata/13_A_Changed_Syntax.php: foo (5 (position 65) - 11 (position 210) (6 lines))";

        self::assertStringContainsString(str_replace(["\n", "\r"], '', $type4Clones), str_replace(["\n", "\r"], '', $output));
    }

    private function assertCommandFailed(): void
    {
        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    /** @group jj */
    public function testDetectsExpectedClones(): void
    {
        $testdataDir = __DIR__ . '/../../testdata/clone-detection-testdata/';
        $reportsDir = __DIR__ . '/../../generated/reports';
        $reportPath = $reportsDir . '/php-dry.json';

        unlink($reportPath);

        self::assertFileDoesNotExist($reportPath);


        $this->commandTester->execute([
            'directory' => $testdataDir,
            '--report_format' => 'json',
            '--reports_directory' => __DIR__ . '/../../generated/reports'
        ]);

        $output = $this->commandTester->getDisplay();

        $expectedOutput = file_get_contents(__DIR__ . '/output.json');
        $expectedOutput = str_replace('%testdata_dir%', $testdataDir, $expectedOutput);

        $this->assertCommandFailed();
        self::assertFileExists($reportPath);
        self::assertJsonStringEqualsJsonString($expectedOutput, \Safe\file_get_contents($reportPath));
    }
}