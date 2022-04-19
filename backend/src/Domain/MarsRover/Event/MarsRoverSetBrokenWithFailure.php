<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover\Event;

use Broadway\Serializer\Serializable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class MarsRoverSetBrokenWithFailure implements Serializable
{
    public function __construct(
        private UuidInterface $id,
        private string $failure
    )
    {
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'failure' => $this->failure
        ];
    }

    public static function deserialize(array $data): self
    {
        return new self(
            Uuid::fromString($data['id']),
            $data['failure']
        );
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getFailure(): string
    {
        return $this->failure;
    }
}