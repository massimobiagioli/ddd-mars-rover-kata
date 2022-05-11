<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover;

use MarsRoverKata\Domain\MarsRover\Obstacles;
use PHPUnit\Framework\TestCase;

class ObstaclesTest extends TestCase
{
    public function test_it_should_create_coordinates_from_array(): void
    {
        $data = [
            ["x" => 1, "y" => 2],
            ["x" => 2, "y" => 3],
        ];
        $obstacles = Obstacles::fromArray($data);

        $this->assertEquals($data, $obstacles->serialize());
    }
}