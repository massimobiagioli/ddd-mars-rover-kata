<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Command\MarsRover;

use MarsRoverKata\Application\Command\MarsRover\PlaceMarsRover;
use MarsRoverKata\Application\Command\MarsRover\RepairMarsRover;
use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Orientation;
use MarsRoverKata\Domain\MarsRover\RepairResult;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class RepairMarsRoverTest extends TestCase
{
    public function test_it_should_create_new_repair_mars_rover_command_with_ok_result(): void
    {
        $id = Uuid::uuid4();
        $result = RepairResult::ok();
        $createdAt = new \DateTimeImmutable();
        $command = new RepairMarsRover($id, $result, $createdAt);

        $this->assertEquals($id->toString(), $command->getId()->toString());
        $this->assertTrue($command->getResult()->isOk());
        $this->assertNull($command->getResult()->failure());
    }

    public function test_it_should_create_new_repair_mars_rover_command_with_ko_result(): void
    {
        $id = Uuid::uuid4();
        $result = RepairResult::ko('failure message');
        $createdAt = new \DateTimeImmutable();
        $command = new RepairMarsRover($id, $result, $createdAt);

        $this->assertEquals($id->toString(), $command->getId()->toString());
        $this->assertFalse($command->getResult()->isOk());
        $this->assertEquals('failure message', $command->getResult()->failure());
    }
}