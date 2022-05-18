<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover\Route;

use MarsRoverKata\Domain\MarsRover\ComplexCommand;
use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Orientation;
use MarsRoverKata\Domain\MarsRover\Route\RouteService;
use MarsRoverKata\Domain\MarsRover\Terrain;
use PHPUnit\Framework\TestCase;

class RouteServiceTest extends TestCase
{
    public function routesProviderWithoutObstacles(): array
    {
        return [
            [
                Terrain::create(9, 9),
                Coordinates::create(0, 0),
                Orientation::fromString('N'),
                ComplexCommand::fromString('L'),
                Coordinates::create(0, 0),
                Orientation::fromString('W'),
                2
            ],
            [
                Terrain::create(9, 9),
                Coordinates::create(0, 0),
                Orientation::fromString('N'),
                ComplexCommand::fromString('F'),
                Coordinates::create(0, 1),
                Orientation::fromString('N'),
                2
            ],
            [
                Terrain::create(9, 9),
                Coordinates::create(0, 0),
                Orientation::fromString('N'),
                ComplexCommand::fromString('FFFF'),
                Coordinates::create(0, 4),
                Orientation::fromString('N'),
                5
            ],
            [
                Terrain::create(9, 9),
                Coordinates::create(0, 0),
                Orientation::fromString('N'),
                ComplexCommand::fromString('FLFLFLL'),
                Coordinates::create(0, 0),
                Orientation::fromString('N'),
                8
            ],
        ];
    }

    /**
     * @dataProvider routesProviderWithoutObstacles
     */
    public function test_it_should_calculate_route_without_obstacles(
        Terrain $terrain,
        Coordinates $departure,
        Orientation $orientation,
        ComplexCommand $command,
        Coordinates $expectedDestination,
        Orientation $expectedOrientation,
        int $steps
    ): void {
        $routeService = new RouteService();
        $route = $routeService->calculateRoute(
            $terrain,
            $departure,
            $orientation,
            $command
        );

        $this->assertTrue($route->destination()->equalsTo($expectedDestination));
        $this->assertTrue($route->orientation()->equalsTo($expectedOrientation));
        $this->assertEquals($steps, $route->steps());
    }
}