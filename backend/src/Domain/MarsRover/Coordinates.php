<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover;

use Webmozart\Assert\Assert;

class Coordinates
{
    private const MIN_X = 0;
    private const MIN_Y = 0;

    private function __construct(private int $x, private int $y)
    {
    }

    public static function create(int $x, int $y): self
    {
        Assert::greaterThanEq($x, self::MIN_X, 'X must be greater or equals to 0');
        Assert::greaterThanEq($y, self::MIN_Y, 'Y must be greater or equals to 0');

        return new self($x, $y);
    }

    public static function fromArray(array $data): self
    {
        Assert::keyExists($data, 'x');
        Assert::keyExists($data, 'y');

        return self::create($data['x'], $data['y']);
    }

    public function serialize(): array
    {
        return ["x" => $this->x, "y" => $this->y];
    }
}
