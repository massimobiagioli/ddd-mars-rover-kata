<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover;

class Status
{
    private const CREATED = 'created';
    private const PLACED = 'placed';
    private const PAUSED = 'paused';
    private const BROKEN = 'broken';

    private function __construct(private string $value)
    {
    }

    public static function created(): self
    {
        return new self(self::CREATED);
    }

    public static function placed(): self
    {
        return new self(self::PLACED);
    }

    public static function paused(): self
    {
        return new self(self::PAUSED);
    }

    public static function broken(): self
    {
        return new self(self::BROKEN);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equalsTo(Status $otherStatus): bool
    {
        return $this->value === $otherStatus->toString();
    }
}