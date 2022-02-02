<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Command\MarsRover;

use MarsRoverKata\Domain\MarsRover\Terrain;
use Ramsey\Uuid\UuidInterface;
use Webmozart\Assert\Assert;

class CreateMarsRover
{
    public function __construct(
        private UuidInterface $id,
        private string $name,
        private Terrain $terrain,
        private \DateTimeImmutable $createdAt
    )
    {
        Assert::notEmpty($name, 'Name cannot be empty');
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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}