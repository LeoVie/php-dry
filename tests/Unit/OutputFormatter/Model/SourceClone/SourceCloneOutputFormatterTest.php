<?php

declare(strict_types=1);

namespace App\Tests\Unit\OutputFormatter\Model\SourceClone;

use App\Collection\MethodsCollection;
use App\Model\Method\Method;
use App\Model\SourceClone\SourceClone;
use App\OutputFormatter\Model\Method\MethodOutputFormatter;
use App\OutputFormatter\Model\SourceClone\SourceCloneOutputFormatter;
use PHPUnit\Framework\TestCase;

class SourceCloneOutputFormatterTest extends TestCase
{
    public function testFormat(): void
    {
        $methodsCollection = $this->createMock(MethodsCollection::class);
        $methodsCollection->method('getAll')->willReturn([
            $this->createMock(Method::class),
            $this->createMock(Method::class),
        ]);

        $methodOutput = $this->createMock(MethodOutputFormatter::class);
        $methodOutput->method('format')->willReturnOnConsecutiveCalls(
            'firstMethod',
            'secondMethod'
        );

        $sourceCloneOutputFormatter = new SourceCloneOutputFormatter(
            $methodOutput
        );

        self::assertSame(
            "CLONE: Type: TYPE_1, Methods: \n\tfirstMethod\n\tsecondMethod",
            $sourceCloneOutputFormatter->format(SourceClone::create(SourceClone::TYPE_1, $methodsCollection))
        );
    }
}