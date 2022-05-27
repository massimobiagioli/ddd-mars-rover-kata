<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover;

use MarsRoverKata\Domain\MarsRover\Route\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    public function test_it_should_create_route_from_array(): void
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
        $this->assertEmpty($route->altRoutes());
        $this->assertFalse($route->hasObstacle());
    }

    public function test_it_should_create_route_with_alt_routes_from_array(): void
    {
        $data = [
            ['coordinates' => ["x" => 1, "y" => 2], 'orientation' => 'N'],
            ['coordinates' => ["x" => 2, "y" => 3], 'orientation' => 'E'],
        ];
        $altRoutes = [
            [
                [
                    'coordinates' => ['x' => 0, 'y' => 0],
                    'orientation' => 'E'
                ],
                [
                    'coordinates' => ['x' => 1, 'y' => 0],
                    'orientation' => 'E'
                ],
                [
                    'coordinates' => ['x' => 1, 'y' => 0],
                    'orientation' => 'N'
                ],
                [
                    'coordinates' => ['x' => 1, 'y' => 1],
                    'orientation' => 'N'
                ],
                [
                    'coordinates' => ['x' => 1, 'y' => 2],
                    'orientation' => 'N'
                ],
                [
                    'coordinates' => ['x' => 1, 'y' => 3],
                    'orientation' => 'N'
                ],
                [
                    'coordinates' => ['x' => 1, 'y' => 3],
                    'orientation' => 'W'
                ],
                [
                    'coordinates' => ['x' => 0, 'y' => 3],
                    'orientation' => 'W'
                ],
                [
                    'coordinates' => ['x' => 0, 'y' => 3],
                    'orientation' => 'N'
                ],
            ]
        ];
        $route = Route::fromArray($data)
            ->withObstacle()
            ->withAltRoutes($altRoutes);

        $this->assertEquals($data, $route->serialize());
        $this->assertEquals(["x" => 2, "y" => 3], $route->destination()->serialize());
        $this->assertEquals('E', $route->orientation()->toString());
        $this->assertEquals(2, $route->steps());
        $this->assertEquals($altRoutes, $route->altRoutes());
        $this->assertTrue($route->hasObstacle());
    }
}