<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Fixtures\MarsRover;

use MarsRoverKata\Domain\MarsRover\MarsRover;
use MarsRoverKata\Domain\MarsRover\Terrain;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class MarsRoverBuilder
{
    private UuidInterface $id;
    private string $name;
    private Terrain $terrain;
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $randomNameSuffix = random_int(0, 1024);
        $this->id = Uuid::uuid4();
        $this->name = "new Mars Rover $randomNameSuffix";
        $this->terrain = Terrain::default();
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function defaults(): self
    {
        return new self();
    }

    public function withId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function withName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function withTerrain(Terrain $terrain): self
    {
        $this->terrain = $terrain;
        return $this;
    }

    public function withCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function get(): MarsRover
    {
        return MarsRover::create(
            $this->id,
            $this->name,
            $this->terrain,
            $this->createdAt
        );
    }
}