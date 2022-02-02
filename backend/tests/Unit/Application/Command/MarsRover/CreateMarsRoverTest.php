<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Command\MarsRover;

use MarsRoverKata\Application\Command\MarsRover\CreateMarsRover;
use MarsRoverKata\Domain\MarsRover\Terrain;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CreateMarsRoverTest extends TestCase
{
    public function test_it_should_create_new_create_mars_rover_command(): void
    {
        $id = Uuid::uuid4();
        $name = "rover-1";
        $terrain = Terrain::default();
        $createdAt = new \DateTimeImmutable();
        $command = new CreateMarsRover(
            $id,
            $name,
            $terrain,
            $createdAt
        );

        $this->assertEquals($id->toString(), $command->getId()->toString());
        $this->assertEquals($name, $command->getName());
        $this->assertEquals($terrain->serialize(), $command->getTerrain()->serialize());
        $this->assertEquals($id->toString(), $command->getId()->toString());
    }

    public function test_it_should_throw_exception_if_empty_name_provided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Name cannot be empty');

        new CreateMarsRover(
            Uuid::uuid4(),
            '',
            Terrain::default(),
            new \DateTimeImmutable()
        );
    }
}