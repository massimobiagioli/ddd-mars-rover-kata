<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover\Event;

use Broadway\Serializer\Serializable;
use MarsRoverKata\Domain\MarsRover\Route\Route;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ObstacleDetected implements Serializable
{
    public function __construct(
        private UuidInterface $id,
        private Route         $route
    )
    {
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'route' => $this->route->serialize()
        ];
    }

    public static function deserialize(array $data): self
    {
        return new self(
            Uuid::fromString($data['id']),
            Route::fromArray($data['route'])
        );
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getRoute(): Route
    {
        return $this->route;
    }
}