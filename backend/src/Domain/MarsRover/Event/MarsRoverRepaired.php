<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover\Event;

use Broadway\Serializer\Serializable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class MarsRoverRepaired implements Serializable
{
    public function __construct(
        private UuidInterface $id,
        private \DateTimeImmutable $maintenanceDate
    )
    {
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'maintenanceDate' => $this->maintenanceDate->format(\DateTimeInterface::ISO8601)
        ];
    }

    public static function deserialize(array $data): self
    {
        return new self(
            Uuid::fromString($data['id']),
            new \DateTimeImmutable($data['maintenanceDate'])
        );
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getMaintenanceDate(): \DateTimeImmutable
    {
        return $this->maintenanceDate;
    }
}