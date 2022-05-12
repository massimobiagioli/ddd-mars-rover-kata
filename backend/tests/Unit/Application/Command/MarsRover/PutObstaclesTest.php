<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Command\MarsRover;

use MarsRoverKata\Application\Command\MarsRover\PutObstacles;
use MarsRoverKata\Domain\MarsRover\Obstacles;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class PutObstaclesTest extends TestCase
{
    public function test_it_should_create_new_put_obstacles_command(): void
    {
        $id = Uuid::uuid4();
        $obstaclesData = [
            ['x' => 1, 'y' => 2],
        ];
        $obstacles = Obstacles::fromArray($obstaclesData);

        $command = new PutObstacles(
            $id,
            $obstacles
        );

        $this->assertEquals($id->toString(), $command->getId()->toString());
        $this->assertEquals($obstaclesData, $command->getObstacles()->serialize());
    }
}