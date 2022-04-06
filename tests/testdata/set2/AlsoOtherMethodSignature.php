<?php /** @noinspection ALL */

class AlsoOtherMethodSignature
{
    public function anotherMethod(): array
    {
        return [1, 2, 3];
    }

    public function sameAsOtherMethodInThisClass(): array
    {
        return $this->anotherMethod();
    }

    public function completelyDifferent(string $text): int
    {
        return strlen($text);
    }
}