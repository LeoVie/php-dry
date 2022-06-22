<?php /** @noinspection ALL */

namespace LeoVie\DTORepository;

class VendorDTO
{
    public function __construct(
        private int $x,
        private int $y
    )
    {
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }
}