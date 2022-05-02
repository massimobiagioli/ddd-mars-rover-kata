<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover\Event;

use Broadway\Serializer\Serializable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class MaintenanceLightTurnedOn implements Serializable
{
    public function __construct(
        private UuidInterface $id,
        private int           $km,
    )
    {
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'km' => $this->km
        ];
    }

    public static function deserialize(array $data): self
    {
        return new self(
            Uuid::fromString($data['id']),
            $data['km']
        );
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getKm(): int
    {
        return $this->km;
    }
}