<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover\Event;

use Broadway\Serializer\Serializable;
use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Orientation;
use MarsRoverKata\Domain\MarsRover\Terrain;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class MarsRoverPlaced implements Serializable
{
    public function __construct(
        private UuidInterface $id,
        private Coordinates   $coordinates,
        private Orientation   $orientation,
    )
    {
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'coordinates' => $this->coordinates->serialize(),
            'orientation' => $this->orientation->toString()
        ];
    }

    public static function deserialize(array $data): self
    {
        return new self(
            Uuid::fromString($data['id']),
            Coordinates::fromArray($data['coordinates']),
            Orientation::fromString($data['orientation'])
        );
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