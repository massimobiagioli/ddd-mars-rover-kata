<?php

namespace MarsRoverKata\Domain\MarsRover\Event;

use Broadway\Serializer\Serializable;
use MarsRoverKata\Domain\MarsRover\Terrain;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class MarsRoverCreated implements Serializable
{
    public function __construct(
        private UuidInterface $id,
        private string $name,
        private Terrain $terrain
    )
    {
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'name' => $this->name,
            'terrain' => $this->terrain->serialize()
        ];
    }

    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['id']),
            $data['name'],
            Terrain::fromArray($data['terrain'])
        );
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTerrain(): Terrain
    {
        return $this->terrain;
    }
}