<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover;

use MarsRoverKata\Domain\MarsRover\Route\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    public function test_it_should_create_coordinates_from_array(): void
    {
        $data = [
            ['coordinates' => ["x" => 1, "y" => 2], 'orientation' => 'N'],
            ['coordinates' => ["x" => 2, "y" => 3], 'orientation' => 'E'],
        ];
        $route = Route::fromArray($data);

        $this->assertEquals($data, $route->serialize());
        $this->assertEquals(["x" => 2, "y" => 3], $route->destination()->serialize());
        $this->assertEquals('E', $route->orientation()->toString());
        $this->assertEquals(2, $route->steps());
    }
}