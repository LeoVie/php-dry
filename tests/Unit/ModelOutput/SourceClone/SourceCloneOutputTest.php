<?php

declare(strict_types=1);

namespace App\Tests\Unit\ModelOutput\SourceClone;

use App\Collection\MethodsCollection;
use App\Model\Method\Method;
use App\Model\SourceClone\SourceClone;
use App\ModelOutput\Method\MethodOutput;
use App\ModelOutput\SourceClone\SourceCloneOutput;
use PHPUnit\Framework\TestCase;

class SourceCloneOutputTest extends TestCase
{
    public function testFormat(): void
    {
        $methodsCollection = $this->createMock(MethodsCollection::class);
        $methodsCollection->method('getAll')->willReturn([
            $this->createMock(Method::class),
            $this->createMock(Method::class),
        ]);

        $methodOutput = $this->createMock(MethodOutput::class);
        $methodOutput->method('format')->willReturnOnConsecutiveCalls(
            'firstMethod',
            'secondMethod'
        );

        $sourceCloneOutput = new SourceCloneOutput(
            $methodOutput
        );

        self::assertSame(
            "CLONE: Type: TYPE_1, Methods: \n\tfirstMethod\n\tsecondMethod",
            $sourceCloneOutput->format(SourceClone::create(SourceClone::TYPE_1, $methodsCollection))
        );
    }
}