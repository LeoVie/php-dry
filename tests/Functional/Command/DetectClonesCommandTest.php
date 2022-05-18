<?php

declare(strict_types=1);

namespace App\Tests\Functional\Command;

use App\Command\DetectClonesCommand;
use DOMDocument;
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
            '--' . DetectClonesCommand::OPTION_CONFIG => __DIR__ . '/php-dry.xml'
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertCommandFailed();
        self::assertStringContainsString('Detecting type 1 clones', $output);
        self::assertStringContainsString('Detecting type 2 clones', $output);
        self::assertStringContainsString('Detecting type 3 clones', $output);
        self::assertStringContainsString('Detecting type 4 by running clones', $output);

        $type1ClonesOutput = "TYPE_1
        ------
        
         * 01_A.php: foo (10 (position 143) - 18 (position 324) (8 lines))
         * 03_A_Exact_Copy.php: foo (10 (position 154) - 18 (position 335) (8 lines))
         * 04_A_Additional_Whitespaces.php: foo (10 (position 166) - 19 (position 369) (9 lines))
         * 05_A_Additional_Comments.php: foo (10 (position 163) - 25 (position 533) (15 lines))
         * 06_A_Changed_Layout.php: foo (10 (position 158) - 14 (position 303) (4 lines))";

        self::assertStringContainsString(
            $this->normalizeCommandLineOutput($type1ClonesOutput),
            $this->normalizeCommandLineOutput($output)
        );

        $type2ClonesOutput = "TYPE_2
        ------
        
         * 01_A.php: foo (10 (position 143) - 18 (position 324) (8 lines))
         * 03_A_Exact_Copy.php: foo (10 (position 154) - 18 (position 335) (8 lines))
         * 04_A_Additional_Whitespaces.php: foo (10 (position 166) - 19 (position 369) (9 lines))
         * 05_A_Additional_Comments.php: foo (10 (position 163) - 25 (position 533) (15 lines))
         * 06_A_Changed_Layout.php: foo (10 (position 158) - 14 (position 303) (4 lines))
         * 07_A_Changed_Variable_Names.php: foo (10 (position 169) - 18 (position 364) (8 lines))
         * 08_A_Changed_Method_Names.php: bar (10 (position 164) - 18 (position 345) (8 lines))
         * 09_A_Changed_Literals.php: foo (10 (position 160) - 18 (position 341) (8 lines))";
        self::assertStringContainsString(
            $this->normalizeCommandLineOutput($type2ClonesOutput),
            $this->normalizeCommandLineOutput($output)
        );

        $type3ClonesOutput = "TYPE_3
        ------
        
         * 01_A.php: foo (10 (position 143) - 18 (position 324) (8 lines))
         * 03_A_Exact_Copy.php: foo (10 (position 154) - 18 (position 335) (8 lines))
         * 04_A_Additional_Whitespaces.php: foo (10 (position 166) - 19 (position 369) (9 lines))
         * 05_A_Additional_Comments.php: foo (10 (position 163) - 25 (position 533) (15 lines))
         * 06_A_Changed_Layout.php: foo (10 (position 158) - 14 (position 303) (4 lines))
         * 07_A_Changed_Variable_Names.php: foo (10 (position 169) - 18 (position 364) (8 lines))
         * 08_A_Changed_Method_Names.php: bar (10 (position 164) - 18 (position 345) (8 lines))
         * 09_A_Changed_Literals.php: foo (10 (position 160) - 18 (position 341) (8 lines))
         * 11_A_Removed_Statements.php: foo (10 (position 162) - 17 (position 324) (7 lines))
         * 12_A_Changed_Statement_Order.php: foo (10 (position 167) - 18 (position 348) (8 lines))";
        self::assertStringContainsString(
            $this->normalizeCommandLineOutput($type3ClonesOutput),
            $this->normalizeCommandLineOutput($output)
        );

        $type4ClonesOutput = "TYPE_4
        ------
        
         * 01_A.php: foo (10 (position 143) - 18 (position 324) (8 lines))
         * 03_A_Exact_Copy.php: foo (10 (position 154) - 18 (position 335) (8 lines))
         * 04_A_Additional_Whitespaces.php: foo (10 (position 166) - 19 (position 369) (9 lines))
         * 05_A_Additional_Comments.php: foo (10 (position 163) - 25 (position 533) (15 lines))
         * 06_A_Changed_Layout.php: foo (10 (position 158) - 14 (position 303) (4 lines))
         * 07_A_Changed_Variable_Names.php: foo (10 (position 169) - 18 (position 364) (8 lines))
         * 08_A_Changed_Method_Names.php: bar (10 (position 164) - 18 (position 345) (8 lines))
         * 09_A_Changed_Literals.php: foo (10 (position 160) - 18 (position 341) (8 lines))
         * 10_A_Additional_Statements.php: foo (10 (position 165) - 19 (position 383) (9 lines))
         * 11_A_Removed_Statements.php: foo (10 (position 162) - 17 (position 324) (7 lines))
         * 12_A_Changed_Statement_Order.php: foo (10 (position 167) - 18 (position 348) (8 lines))
         * 13_A_Changed_Syntax.php: foo (10 (position 158) - 16 (position 299) (6 lines))";
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

    /** @group now */
    public function testJsonReport(): void
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
            '--' . DetectClonesCommand::OPTION_CONFIG => __DIR__ . '/php-dry.xml'
        ]);

        $expectedJson = str_replace(
            '%testdata_dir%',
            $testdataDir,
            \Safe\file_get_contents(__DIR__ . '/expected_php-dry.json')
        );

        $this->assertCommandFailed();

        self::assertFileExists($reportPath);

        $actualJson = \Safe\file_get_contents($reportPath);

        self::assertJsonStringEqualsJsonString($expectedJson, $actualJson);
    }

    public function testHtmlReport(): void
    {
        $testdataDir = __DIR__ . '/../../testdata/clone-detection-testdata/';
        $reportsDir = __DIR__ . '/../../generated/reports/';
        $reportPath = $reportsDir . 'php-dry_html-report/php-dry.html';

        if (file_exists($reportPath)) {
            unlink($reportPath);
        }

        self::assertFileDoesNotExist($reportPath);

        $this->commandTester->execute([
            DetectClonesCommand::ARGUMENT_DIRECTORY => $testdataDir,
            '--' . DetectClonesCommand::OPTION_CONFIG => __DIR__ . '/php-dry.xml'
        ]);

        $expectedHtml = str_replace(
            '%testdata_dir%',
            $testdataDir,
            \Safe\file_get_contents(__DIR__ . '/expected_php-dry.html')
        );

        $this->assertCommandFailed();

        self::assertFileExists($reportPath);

        $actualHtml = \Safe\file_get_contents($reportPath);

        self::assertHtmlStringEqualsHtmlString($expectedHtml, $actualHtml);
    }

    protected function assertHtmlStringEqualsHtmlString(string $expectedHtml, string $actualHtml)
    {
        $this->assertEqualsCanonicalizing(
            $this->convertToDomDocument($expectedHtml),
            $this->convertToDomDocument($actualHtml),
        );
    }

    protected function convertToDomDocument(string $html): DOMDocument
    {
        $html = preg_replace(' @\r@', '', $html);
        $html = preg_replace(' @\n@', '', $html);
        $html = preg_replace('/>\s+</', '><', $html);
        $html = preg_replace('@\s+</@', '</', $html);
        $html = preg_replace('@>\s\s+@', '>', $html);
        $html = html_entity_decode($html);

        $domDocument = new DOMDocument();

        $domDocument->loadHTML($html, LIBXML_NOERROR);
        $domDocument->preserveWhiteSpace = false;

        return $domDocument;
    }
}
