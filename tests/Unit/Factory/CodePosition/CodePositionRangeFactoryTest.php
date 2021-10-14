<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory\CodePosition;

use App\Factory\CodePosition\CodePositionFactory;
use App\Factory\CodePosition\CodePositionRangeFactory;
use App\Model\CodePosition\CodePosition;
use App\Model\CodePosition\CodePositionRange;
use PhpParser\Node\Stmt\Function_;
use PHPUnit\Framework\TestCase;

class CodePositionRangeFactoryTest extends TestCase
{
    /** @dataProvider byClassMethodOrFunctionProvider */
    public function testByClassMethodOrFunction(CodePositionRange $expected, CodePosition $start, CodePosition $end): void
    {
        $codePositionFactory = $this->createMock(CodePositionFactory::class);
        $codePositionFactory->method('byStartClassMethodOrFunction')->willReturn($start);
        $codePositionFactory->method('byEndClassMethodOrFunction')->willReturn($end);

        $function = $this->createMock(Function_::class);

        self::assertEquals($expected, (new CodePositionRangeFactory($codePositionFactory))->byClassMethodOrFunction($function));
    }

    public function byClassMethodOrFunctionProvider(): array
    {
        $start = CodePosition::create(10, 50);
        $end = CodePosition::create(100, 7);

        return [
            [
                'expected' => CodePositionRange::create($start, $end),
                'start' => $start,
                'end' => $end,
            ],
        ];
    }
}