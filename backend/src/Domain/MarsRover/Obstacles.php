<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover;

class Obstacles
{
    private function __construct(private array $data)
    {
    }

    public static function fromArray(array $data): self
    {
        $coordinatesCollection = array_map(function (array $coordinates) {
            return Coordinates::fromArray($coordinates);
        }, $data);

        return new self($coordinatesCollection);
    }

    public function serialize(): array
    {
        return array_reduce($this->data, function (array $carry, Coordinates $coordinates) {
            $carry[] = $coordinates->serialize();
            return $carry;
        }, []);
    }
}
