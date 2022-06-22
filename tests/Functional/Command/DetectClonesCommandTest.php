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
        $this->commandTester->execute([
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
        
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/01_A.php: foo (12 (position 202) - 20 (position 383) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/03_A_Exact_Copy.php: foo (12 (position 213) - 20 (position 394) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/04_A_Additional_Whitespaces.php: foo (12 (position 225) - 21 (position 428) (9 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/05_A_Additional_Comments.php: foo (12 (position 222) - 27 (position 592) (15 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/06_A_Changed_Layout.php: foo (12 (position 217) - 16 (position 362) (4 lines))";

        self::assertStringContainsString(
            $this->normalizeCommandLineOutput($type1ClonesOutput),
            $this->normalizeCommandLineOutput($output)
        );

        $type2ClonesOutput = "TYPE_2
        ------
        
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/01_A.php: foo (12 (position 202) - 20 (position 383) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/03_A_Exact_Copy.php: foo (12 (position 213) - 20 (position 394) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/04_A_Additional_Whitespaces.php: foo (12 (position 225) - 21 (position 428) (9 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/05_A_Additional_Comments.php: foo (12 (position 222) - 27 (position 592) (15 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/06_A_Changed_Layout.php: foo (12 (position 217) - 16 (position 362) (4 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/07_A_Changed_Variable_Names.php: foo (12 (position 228) - 20 (position 423) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/08_A_Changed_Method_Names.php: bar (12 (position 223) - 20 (position 404) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/09_A_Changed_Literals.php: foo (12 (position 219) - 20 (position 400) (8 lines))";
        self::assertStringContainsString(
            $this->normalizeCommandLineOutput($type2ClonesOutput),
            $this->normalizeCommandLineOutput($output)
        );

        $type3ClonesOutput = "TYPE_3
        ------
        
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/01_A.php: foo (12 (position 202) - 20 (position 383) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/03_A_Exact_Copy.php: foo (12 (position 213) - 20 (position 394) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/04_A_Additional_Whitespaces.php: foo (12 (position 225) - 21 (position 428) (9 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/05_A_Additional_Comments.php: foo (12 (position 222) - 27 (position 592) (15 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/06_A_Changed_Layout.php: foo (12 (position 217) - 16 (position 362) (4 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/07_A_Changed_Variable_Names.php: foo (12 (position 228) - 20 (position 423) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/08_A_Changed_Method_Names.php: bar (12 (position 223) - 20 (position 404) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/09_A_Changed_Literals.php: foo (12 (position 219) - 20 (position 400) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/11_A_Removed_Statements.php: foo (12 (position 221) - 19 (position 383) (7 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/12_A_Changed_Statement_Order.php: foo (12 (position 226) - 20 (position 407) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/14_A_Changed_Param_Order.php: foo (12 (position 222) - 20 (position 403) (8 lines))";
        self::assertStringContainsString(
            $this->normalizeCommandLineOutput($type3ClonesOutput),
            $this->normalizeCommandLineOutput($output)
        );

        $type4ClonesOutput = "TYPE_4
        ------
        
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/01_A.php: foo (12 (position 202) - 20 (position 383) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/03_A_Exact_Copy.php: foo (12 (position 213) - 20 (position 394) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/04_A_Additional_Whitespaces.php: foo (12 (position 225) - 21 (position 428) (9 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/05_A_Additional_Comments.php: foo (12 (position 222) - 27 (position 592) (15 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/06_A_Changed_Layout.php: foo (12 (position 217) - 16 (position 362) (4 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/07_A_Changed_Variable_Names.php: foo (12 (position 228) - 20 (position 423) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/08_A_Changed_Method_Names.php: bar (12 (position 223) - 20 (position 404) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/09_A_Changed_Literals.php: foo (12 (position 219) - 20 (position 400) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/10_A_Additional_Statements.php: foo (12 (position 224) - 21 (position 442) (9 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/11_A_Removed_Statements.php: foo (12 (position 221) - 19 (position 383) (7 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/12_A_Changed_Statement_Order.php: foo (12 (position 226) - 20 (position 407) (8 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/13_A_Changed_Syntax.php: foo (12 (position 217) - 18 (position 358) (6 lines))
         * /app/tests/testdata/clone-detection-testdata-with-native-types/src/14_A_Changed_Param_Order.php: foo (12 (position 222) - 20 (position 403) (8 lines))";
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

    public function testJsonReport(): void
    {
        $testdataDir = __DIR__ . '/../../testdata/clone-detection-testdata-with-native-types/src/';
        $reportsDir = __DIR__ . '/../../generated/reports';
        $reportPath = $reportsDir . '/php-dry.json';

        if (file_exists($reportPath)) {
            unlink($reportPath);
        }

        self::assertFileDoesNotExist($reportPath);

        $this->commandTester->execute([
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
        $testdataDir = __DIR__ . '/../../testdata/clone-detection-testdata-with-native-types/src/';
        $reportsDir = __DIR__ . '/../../generated/reports/';
        $reportPath = $reportsDir . 'php-dry_html-report/php-dry.html';

        if (file_exists($reportPath)) {
            unlink($reportPath);
        }

        self::assertFileDoesNotExist($reportPath);

        $this->commandTester->execute([
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

    /** @group now */
    public function testWithDTOParams(): void
    {
        $this->commandTester->execute([
            '--' . DetectClonesCommand::OPTION_CONFIG => __DIR__ . '/php-dry_with-DTO-params.xml'
        ]);

        $output = $this->commandTester->getDisplay();
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
