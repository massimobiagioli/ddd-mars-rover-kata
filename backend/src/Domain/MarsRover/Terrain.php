<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover;

use Webmozart\Assert\Assert;

class Terrain
{
    private const MIN_HEIGHT = 3;
    private const MIN_WIDTH = 3;
    private const DEFAULT_HEIGHT = 20;
    private const DEFAULT_WIDTH = 20;

    private function __construct(private int $height, private int $width, private ?Obstacles $obstacles = null)
    {
    }

    public static function create(int $height, int $width, ?Obstacles $obstacles = null): self
    {
        Assert::greaterThanEq($height, self::MIN_HEIGHT, 'Height must be at least 3');
        Assert::greaterThanEq($width, self::MIN_WIDTH, 'Width must be at least 3');

        return new self($height, $width, $obstacles);
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

    public function capX(int $x): int
    {
        return $x < 0 ? 0 : (min($x, $this->height));
    }

    public function capY(int $y): int
    {
        return $y < 0 ? 0 : (min($y, $this->width));
    }

    public function withObstacles(Obstacles $obstacles): self
    {
        return new self($this->height, $this->width, $obstacles);
    }

    public function serialize(): array
    {
        return [
            "height" => $this->height,
            "width" => $this->width,
            "obstacles" => $this->obstacles ? $this->obstacles->serialize() : []
        ];
    }
}
