<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover;

use Webmozart\Assert\Assert;

class PrimitiveCommand
{
    private const ALLOWED_VALUES = ['F', 'B', 'L', 'R'];
    private const COMMANDS_THAT_UPDATES_KM = ['F', 'B'];

    private function __construct(private string $value)
    {
    }

    public static function fromString(string $value): self
    {
        Assert::inArray($value, self::ALLOWED_VALUES, "Value $value is not valid for primitive command");

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function in(array $values): bool
    {
        return in_array($this->value, $values);
    }

    public function canUpdateKm(): bool
    {
        return $this->in(self::COMMANDS_THAT_UPDATES_KM);
    }
}
