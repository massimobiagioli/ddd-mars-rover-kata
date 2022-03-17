<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover;

class Status
{
    private const CREATED = 'created';
    private const PLACED = 'placed';

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

    public function toString(): string
    {
        return $this->value;
    }
}