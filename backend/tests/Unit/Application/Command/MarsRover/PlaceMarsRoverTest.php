<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Command\MarsRover;

use MarsRoverKata\Application\Command\MarsRover\PlaceMarsRover;
use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Orientation;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class PlaceMarsRoverTest extends TestCase
{
    public function test_it_should_create_new_place_mars_rover_command(): void
    {
        $id = Uuid::uuid4();
        $coordinates = Coordinates::create(1, 2);
        $orientation = Orientation::fromString('E');
        $command = new PlaceMarsRover(
            $id,
            $coordinates,
            $orientation
        );

        $this->assertEquals($id->toString(), $command->getId()->toString());
        $this->assertEquals($coordinates->serialize(), $command->getCoordinates()->serialize());
        $this->assertEquals($orientation->toString(), $command->getOrientation()->toString());
    }
}