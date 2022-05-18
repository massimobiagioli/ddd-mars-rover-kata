<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover\Route;

use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Orientation;

class Route
{
    private function __construct(private array $data)
    {
    }

    public static function fromArray(array $data): self
    {
        $coordinatesCollection = array_map(function (array $entry) {
            return [
                'coordinates' => Coordinates::fromArray($entry['coordinates']),
                'orientation' => Orientation::fromString($entry['orientation']),
            ];
        }, $data);

        return new self($coordinatesCollection);
    }

    public function serialize(): array
    {
        return array_reduce($this->data, function (array $carry, array $entry) {
            $carry[] = [
                'coordinates' => $entry['coordinates']->serialize(),
                'orientation' => $entry['orientation']->toString()
            ];
            return $carry;
        }, []);
    }

    public function destination(): Coordinates
    {
        return $this->data[count($this->data) - 1]['coordinates'];
    }

    public function orientation(): Orientation
    {
        return $this->data[count($this->data) - 1]['orientation'];
    }

    public function steps(): int
    {
        return count($this->data);
    }
}
