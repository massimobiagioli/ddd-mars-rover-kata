<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverCreated;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverPlaced;
use Ramsey\Uuid\UuidInterface;

class MarsRover extends EventSourcedAggregateRoot
{
    private UuidInterface $id;
    private string $name;
    private Terrain $terrain;
    private \DateTimeImmutable $createdAt;
    private ?Coordinates $coordinates;
    private ?Orientation $orientation;

    private function __construct()
    {
        $this->coordinates = null;
        $this->orientation = null;
    }

    public static function create(
        UuidInterface $id,
        string $name,
        Terrain $terrain,
        \DateTimeImmutable $createdAt
    ): self {
        $marsRover = new self();
        $marsRover->apply(new MarsRoverCreated(
            $id,
            $name,
            $terrain,
            $createdAt
        ));
        return $marsRover;
    }

    public function place(
        Coordinates $coordinates,
        Orientation $orientation
    ): void {
        $this->coordinates = $coordinates;
        $this->orientation = $orientation;

        // TODO: Verifica se le coordinate sono valide per il terreno

        $this->apply(new MarsRoverPlaced(
            $this->id,
            $coordinates,
            $orientation
        ));
    }

    public function getAggregateRootId(): string
    {
        return $this->id->toString();
    }

    protected function applyMarsRoverCreated(MarsRoverCreated $event) {
        $this->id = $event->getId();
        $this->name = $event->getName();
        $this->terrain = $event->getTerrain();
        $this->createdAt = $event->getCreatedAt();
    }
}