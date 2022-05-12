<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover\Event;

use Broadway\Serializer\Serializable;
use MarsRoverKata\Domain\MarsRover\Obstacles;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class TerrainUpdatedWithObstacles implements Serializable
{
    public function __construct(
        private UuidInterface $id,
        private Obstacles     $obstacles
    )
    {
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'obstacles' => $this->obstacles->serialize()
        ];
    }

    public static function deserialize(array $data): self
    {
        return new self(
            Uuid::fromString($data['id']),
            Obstacles::fromArray($data['obstacles'])
        );
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getObstacles(): Obstacles
    {
        return $this->obstacles;
    }
}