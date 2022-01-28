<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\Terrain;

use Webmozart\Assert\Assert;

class Terrain
{
    private const MIN_HEIGHT = 3;
    private const MIN_WIDTH = 3;

    private function __construct(private int $height, private int $width)
    {
    }

    public static function create(int $height, int $width): self
    {
        Assert::greaterThanEq($height, self::MIN_HEIGHT, 'Height must be at least 3');
        Assert::greaterThanEq($width, self::MIN_WIDTH, 'Width must be at least 3');

        return new self($height, $width);
    }

    public function height(): int
    {
        return $this->height;
    }

    public function width(): int
    {
        return $this->width;
    }
}
