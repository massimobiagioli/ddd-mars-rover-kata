<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover\Event;

use Broadway\Serializer\Serializable;
use MarsRoverKata\Domain\MarsRover\ComplexCommand;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ComplexCommandSent implements Serializable
{
    public function __construct(
        private UuidInterface    $id,
        private ComplexCommand $complexCommand
    )
    {
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'complex_command' => $this->complexCommand->toString()
        ];
    }

    public static function deserialize(array $data): self
    {
        return new self(
            Uuid::fromString($data['id']),
            ComplexCommand::fromString($data['complex_command'])
        );
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getComplexCommand(): ComplexCommand
    {
        return $this->complexCommand;
    }
}