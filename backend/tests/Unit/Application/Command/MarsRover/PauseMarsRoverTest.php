<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Command\MarsRover;

use MarsRoverKata\Application\Command\MarsRover\PauseMarsRover;
use MarsRoverKata\Application\Command\MarsRover\PlaceMarsRover;
use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Orientation;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class PauseMarsRoverTest extends TestCase
{
    public function test_it_should_create_new_pause_mars_rover_command(): void
    {
        $id = Uuid::uuid4();
        $command = new PauseMarsRover($id);

        $this->assertEquals($id->toString(), $command->getId()->toString());
    }
}