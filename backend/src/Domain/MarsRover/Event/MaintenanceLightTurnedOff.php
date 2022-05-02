<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover\Event;

use Broadway\Serializer\Serializable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class MaintenanceLightTurnedOff implements Serializable
{
    public function __construct(
        private UuidInterface $id
    )
    {
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id->toString()
        ];
    }

    public static function deserialize(array $data): self
    {
        return new self(
            Uuid::fromString($data['id'])
        );
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }
}