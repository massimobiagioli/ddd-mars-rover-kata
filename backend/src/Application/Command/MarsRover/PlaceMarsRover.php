<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Command\MarsRover;

use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Orientation;
use MarsRoverKata\Domain\MarsRover\Terrain;
use Ramsey\Uuid\UuidInterface;
use Webmozart\Assert\Assert;

class PlaceMarsRover
{
    public function __construct(
        private UuidInterface $id,
        private Coordinates $coordinates,
        private Orientation $orientation
    )
    {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getCoordinates(): Coordinates
    {
        return $this->coordinates;
    }

    public function getOrientation(): Orientation
    {
        return $this->orientation;
    }
}