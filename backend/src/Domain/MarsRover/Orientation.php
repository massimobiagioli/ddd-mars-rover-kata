<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover;

use Webmozart\Assert\Assert;

class Orientation
{
    private const ALLOWED_VALUES = ['N', 'S', 'E', 'W'];

    private function __construct(private string $value)
    {
    }

    public static function fromString(string $value): self
    {
        Assert::inArray($value, self::ALLOWED_VALUES, "Value $value is not valid for orientation");

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
