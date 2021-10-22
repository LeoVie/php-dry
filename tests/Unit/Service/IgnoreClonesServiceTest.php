<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Collection\MethodsCollection;
use App\Configuration\Configuration;
use App\Model\CodePosition\CodePositionRange;
use App\Model\Method\Method;
use App\Model\SourceClone\SourceClone;
use App\Service\IgnoreClonesService;
use App\Util\ArrayUtil;
use PHPUnit\Framework\TestCase;

class IgnoreClonesServiceTest extends TestCase
{
    public function testFoo(): void
    {
        self::markTestSkipped();
    }

//    /** @dataProvider extractNonIgnoredClonesProvider */
//    public function testExtractNonIgnoredClones(array $expected, array $cloneGroups, Configuration $configuration): void
//    {
//        self::assertSame($expected, (new IgnoreClonesService(new ArrayUtil()))->extractNonIgnoredClones($cloneGroups, $configuration));
//    }
//
//    public function extractNonIgnoredClonesProvider(): \Generator
//    {
//        $configuration = Configuration::create('', 5, 0);
//        yield 'empty' => [
//            'expected' => [],
//            'cloneGroups' => [],
//            'configuration' => $configuration,
//        ];
//
//        $configuration = Configuration::create('', 5, 0);
//        $method1 = $this->mockMethod(4);
//        $method2 = $this->mockMethod(6);
//        $method3 = $this->mockMethod(4);
//        $method4 = $this->mockMethod(1);
//        $clone1 = $this->mockSourceClone([$method1, $method2]);
//        $clone2 = $this->mockSourceClone([$method3, $method4]);
//        yield 'non empty' => [
//            'expected' => [$clone1],
//            'cloneGroups' => [
//                [$clone1],
//                [$clone2],
//            ],
//            'configuration' => $configuration,
//        ];
//    }
//
//    private function mockMethod(int $countOfLines): Method
//    {
//        $method = $this->createMock(Method::class);
//        $codePositionRange = $this->createMock(CodePositionRange::class);
//        $codePositionRange->method('countOfLines')->willReturn($countOfLines);
//        $method->method('getCodePositionRange')->willReturn($codePositionRange);
//
//        return $method;
//    }
//
//    private function mockSourceClone(array $methods): SourceClone
//    {
//        $sourceClone = $this->createMock(SourceClone::class);
//        $methodsCollection = $this->createMock(MethodsCollection::class);
//        $methodsCollection->method('getAll')->willReturn($methods);
//        $sourceClone->method('getMethodsCollection')->willReturn($methodsCollection);
//
//        return $sourceClone;
//    }
}