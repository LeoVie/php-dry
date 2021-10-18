<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Factory\FilepathMethodsFactory;
use App\Factory\MethodFactory;
use App\Model\FilepathMethods\FilepathMethods;
use App\Model\Method\Method;
use App\Service\FindMethodsInPathsService;
use PHPUnit\Framework\TestCase;

class FindMethodsInPathsServiceTest extends TestCase
{
    public function testFind(): void
    {
        $filepathMethodsFactory = $this->createMock(FilepathMethodsFactory::class);
        $filepathMethodsFactory->method('create')->willReturn([
            $this->createMock(FilepathMethods::class),
            $this->createMock(FilepathMethods::class),
        ]);

        $methods = [
            $this->createMock(Method::class),
            $this->createMock(Method::class),
            $this->createMock(Method::class),
            $this->createMock(Method::class),
            $this->createMock(Method::class),
            $this->createMock(Method::class),
        ];
        $methodFactory = $this->createMock(MethodFactory::class);
        $methodFactory->method('buildMultipleFromFilepathMethods')->willReturnOnConsecutiveCalls(
            [
                $methods[0],
                $methods[1],
            ],
            [
                $methods[2],
                $methods[3],
                $methods[4],
                $methods[5],
            ],
        );

        self::assertSame($methods, (new FindMethodsInPathsService($filepathMethodsFactory, $methodFactory))->find([]));
    }
}