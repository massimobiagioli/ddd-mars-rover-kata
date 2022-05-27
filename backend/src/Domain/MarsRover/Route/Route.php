<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover\Route;

use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Orientation;

class Route
{
    private array $altRoutes;
    private bool $foundObstacle;

    private function __construct(private array $data)
    {
        $this->altRoutes = [];
        $this->foundObstacle = false;
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

    public function withAltRoutes(array $altRoutes): self
    {
        $this->altRoutes = $altRoutes;
        return $this;
    }

    public function withObstacle(): self
    {
        $this->foundObstacle = true;
        return $this;
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
        $index = count($this->data) > 0 ? count($this->data) - 1 : 0;
        return $this->data[$index]['coordinates'];
    }

    public function orientation(): Orientation
    {
        $index = count($this->data) > 0 ? count($this->data) - 1 : 0;
        return $this->data[$index]['orientation'];
    }

    public function steps(): int
    {
        return count($this->data);
    }

    public function altRoutes(): array
    {
        return array_reduce($this->altRoutes, function (array $carry, Route $route) {
            $carry[] = $route->serialize();
            return $carry;
        }, []);
    }

    public function hasObstacle(): bool
    {
        return $this->foundObstacle;
    }
}
