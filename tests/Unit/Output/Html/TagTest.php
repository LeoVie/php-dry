<?php

namespace App\Tests\Unit\Output\Html;

use App\Output\Html\Attribute;
use App\Output\Html\Content;
use App\Output\Html\Tag;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    /** @dataProvider asCodeProvider */
    public function testAsCode(string $expected, Tag $tag): void
    {
        self::assertSame($expected, $tag->asCode());
    }

    public function asCodeProvider(): array
    {
        return [
            [
                'expected' => '<h1 >Foo</h1>',
                'attribute' => Tag::create('h1', [], [Content::create('Foo')]),
            ],
            [
                'expected' => '<h1 >Foo</h1>',
                'attribute' => Tag::create('h1', [], [Content::create('Foo')]),
            ],
            [
                'expected' => '<h1 >Click <a href="#">here</a></h1>',
                'attribute' => Tag::create(
                    'h1',
                    [],
                    [
                        Content::create('Click '),
                        Tag::create(
                            'a',
                            [
                                Attribute::create('href', '#'),
                            ],
                            [
                                Content::create('here')
                            ]
                        ),
                    ]
                ),
            ],
        ];
    }
}