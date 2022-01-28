<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover;

use Webmozart\Assert\Assert;

class Terrain
{
    private const MIN_HEIGHT = 3;
    private const MIN_WIDTH = 3;
    private const DEFAULT_HEIGHT = 10;
    private const DEFAULT_WIDTH = 10;

    private function __construct(private int $height, private int $width)
    {
    }

    public static function create(int $height, int $width): self
    {
        Assert::greaterThanEq($height, self::MIN_HEIGHT, 'Height must be at least 3');
        Assert::greaterThanEq($width, self::MIN_WIDTH, 'Width must be at least 3');

        return new self($height, $width);
    }

    public static function default(): self
    {
        return self::create(self::DEFAULT_HEIGHT, self::DEFAULT_WIDTH);
    }

    public static function fromArray(array $data): self
    {
        Assert::keyExists($data, 'height');
        Assert::keyExists($data, 'width');

        return self::create($data['height'], $data['width']);
    }

    public function serialize(): array
    {
        return ["height" => $this->height, "width" => $this->width];
    }
}
