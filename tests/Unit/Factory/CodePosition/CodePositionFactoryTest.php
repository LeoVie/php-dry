<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory\CodePosition;

use App\Factory\CodePosition\CodePositionFactory;
use App\Model\CodePosition\CodePosition;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPUnit\Framework\TestCase;

class CodePositionFactoryTest extends TestCase
{
    /** @dataProvider byStartClassMethodOrFunctionProvider */
    public function testByStartClassMethodOrFunction(CodePosition $expected, ClassMethod|Function_ $function): void
    {
        self::assertEquals($expected, (new CodePositionFactory())->byStartClassMethodOrFunction($function));
    }

    public function byStartClassMethodOrFunctionProvider(): array
    {
        return [
            'ClassMethod (#1)' => [
                'expected' => CodePosition::create(10, 0),
                'function' => $this->mockClassMethod([10, 0], [20, 5]),
            ],
            'ClassMethod (#2)' => [
                'expected' => CodePosition::create(99, 12),
                'function' => $this->mockClassMethod([99, 12], [750, 50]),
            ],
            'Function (#1)' => [
                'expected' => CodePosition::create(10, 0),
                'function' => $this->mockFunction([10, 0], [20, 5]),
            ],
            'Function (#2)' => [
                'expected' => CodePosition::create(99, 12),
                'function' => $this->mockFunction([99, 12], [750, 50]),
            ],
        ];
    }

    /** @dataProvider byEndClassMethodOrFunctionProvider */
    public function testByEndClassMethodOrFunction(CodePosition $expected, ClassMethod|Function_ $function): void
    {
        self::assertEquals($expected, (new CodePositionFactory())->byEndClassMethodOrFunction($function));
    }

    public function byEndClassMethodOrFunctionProvider(): array
    {
        return [
            'ClassMethod (#1)' => [
                'expected' => CodePosition::create(20, 5),
                'function' => $this->mockClassMethod([10, 0], [20, 5]),
            ],
            'ClassMethod (#2)' => [
                'expected' => CodePosition::create(750, 50),
                'function' => $this->mockClassMethod([99, 12], [750, 50]),
            ],
            'Function (#1)' => [
                'expected' => CodePosition::create(20, 5),
                'function' => $this->mockFunction([10, 0], [20, 5]),
            ],
            'Function (#2)' => [
                'expected' => CodePosition::create(750, 50),
                'function' => $this->mockFunction([99, 12], [750, 50]),
            ],
        ];
    }

    private function mockClassMethod(array $start, array $end): ClassMethod
    {
        $classMethod = $this->createMock(ClassMethod::class);
        $classMethod->method('getStartLine')->willReturn($start[0]);
        $classMethod->method('getStartFilePos')->willReturn($start[1]);
        $classMethod->method('getEndLine')->willReturn($end[0]);
        $classMethod->method('getEndFilePos')->willReturn($end[1]);

        return $classMethod;
    }

    private function mockFunction(array $start, array $end): Function_
    {
        $function = $this->createMock(Function_::class);
        $function->method('getStartLine')->willReturn($start[0]);
        $function->method('getStartFilePos')->willReturn($start[1]);
        $function->method('getEndLine')->willReturn($end[0]);
        $function->method('getEndFilePos')->willReturn($end[1]);

        return $function;
    }
}
