<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover\Event;

use Broadway\Serializer\Serializable;
use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Orientation;
use MarsRoverKata\Domain\MarsRover\PrimitiveCommand;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class PrimitiveCommandSent implements Serializable
{
    public function __construct(
        private UuidInterface    $id,
        private PrimitiveCommand $primitiveCommand
    )
    {
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'primitive_command' => $this->primitiveCommand->toString()
        ];
    }

    public static function deserialize(array $data): self
    {
        return new self(
            Uuid::fromString($data['id']),
            PrimitiveCommand::fromString($data['primitive_command'])
        );
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getPrimitiveCommand(): PrimitiveCommand
    {
        return $this->primitiveCommand;
    }
}