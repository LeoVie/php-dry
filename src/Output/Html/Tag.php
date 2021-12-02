<?php

namespace App\Output\Html;

class Tag
{
    /**
     * @param Attribute[] $attributes
     * @param array<Tag|Content> $childs
     */
    private function __construct(
        private string $name,
        private array  $attributes,
        private array  $childs
    )
    {
    }

    /**
     * @param Attribute[] $attributes
     * @param array<Tag|Content> $childs
     */
    public static function create(string $name, array $attributes, array $childs): self
    {
        return new self($name, $attributes, $childs);
    }

    public function asCode(): string
    {
        return \Safe\sprintf(
            '<%s %s>%s</%s>',
            $this->name,
            join(' ', array_map(fn(Attribute $attribute): string => $attribute->asCode(), $this->attributes)),
            join('', array_map(fn(Tag|Content $tag): string => $tag->asCode(), $this->childs)),
            $this->name
        );
    }
}