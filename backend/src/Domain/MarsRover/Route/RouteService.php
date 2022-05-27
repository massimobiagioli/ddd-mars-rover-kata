<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover\Route;

use MarsRoverKata\Domain\MarsRover\ComplexCommand;
use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Orientation;
use MarsRoverKata\Domain\MarsRover\Terrain;

class RouteService
{
    private const COMMAND_STATUS_MAP = [
        'F' => [
            'N' => [
                'offsetX' => 0,
                'offsetY' => 1,
                'newOrientation' => 'N'
            ],
            'S' => [
                'offsetX' => 0,
                'offsetY' => -1,
                'newOrientation' => 'S'
            ],
            'E' => [
                'offsetX' => 1,
                'offsetY' => 0,
                'newOrientation' => 'E'
            ],
            'W' => [
                'offsetX' => -1,
                'offsetY' => 0,
                'newOrientation' => 'W'
            ]
        ],
        'B' => [
            'N' => [
                'offsetX' => 0,
                'offsetY' => -1,
                'newOrientation' => 'N'
            ],
            'S' => [
                'offsetX' => 0,
                'offsetY' => 1,
                'newOrientation' => 'S'
            ],
            'E' => [
                'offsetX' => -1,
                'offsetY' => 0,
                'newOrientation' => 'E'
            ],
            'W' => [
                'offsetX' => 1,
                'offsetY' => 0,
                'newOrientation' => 'W'
            ]
        ],
        'L' => [
            'N' => [
                'offsetX' => 0,
                'offsetY' => 0,
                'newOrientation' => 'W'
            ],
            'S' => [
                'offsetX' => 0,
                'offsetY' => 0,
                'newOrientation' => 'E'
            ],
            'E' => [
                'offsetX' => 0,
                'offsetY' => 0,
                'newOrientation' => 'N'
            ],
            'W' => [
                'offsetX' => 0,
                'offsetY' => 0,
                'newOrientation' => 'S'
            ]
        ],
        'R' => [
            'N' => [
                'offsetX' => 0,
                'offsetY' => 0,
                'newOrientation' => 'E'
            ],
            'S' => [
                'offsetX' => 0,
                'offsetY' => 0,
                'newOrientation' => 'W'
            ],
            'E' => [
                'offsetX' => 0,
                'offsetY' => 0,
                'newOrientation' => 'S'
            ],
            'W' => [
                'offsetX' => 0,
                'offsetY' => 0,
                'newOrientation' => 'N'
            ]
        ]
    ];

    private const TO_THE_RIGHT_COMPLEX_COMMAND = 'RFLFFLFR';

    public function calculateRoute(
        Terrain        $terrain,
        Coordinates    $coordinates,
        Orientation    $orientation,
        ComplexCommand $command
    ): Route
    {
        $simulatedRouteData = $this->routeDataFromComplexCommand(
            $terrain,
            $coordinates,
            $orientation,
            $command
        );
        $simulatedRoute = Route::fromArray($simulatedRouteData['data']);

        if (!$simulatedRouteData['hasObstacle']) {
            return $simulatedRoute;
        }

        $altRoutes = $this->findAltRoutes(
            $terrain,
            $coordinates,
            $orientation,
            $command,
            $simulatedRouteData
        );

        return $simulatedRoute
            ->withObstacle()
            ->withAltRoutes($altRoutes);
    }

    private function routeDataFromComplexCommand(
        Terrain        $terrain,
        Coordinates    $coordinates,
        Orientation    $orientation,
        ComplexCommand $command
    ): array
    {
        $routeData = [];
        $hasObstacle = false;
        $index = 0;
        $obstacleIndex = 0;
        foreach ($command->getPrimitiveCommands() as $primitiveCommand) {
            $commandMapEntry = self::COMMAND_STATUS_MAP[$primitiveCommand->toString()][$orientation->toString()];

            $newX = $terrain->capX($coordinates->x() + $commandMapEntry['offsetX']);
            $newY = $terrain->capY($coordinates->y() + $commandMapEntry['offsetY']);
            if ($terrain->obstacleAt($newX, $newY)) {
                $hasObstacle = true;
                $obstacleIndex = $index;
            }

            $coordinates = Coordinates::create(
                $terrain->capX($coordinates->x() + $commandMapEntry['offsetX']),
                $terrain->capY($coordinates->y() + $commandMapEntry['offsetY'])
            );

            $orientation = Orientation::fromString($commandMapEntry['newOrientation']);

            $routeData[] = [
                'coordinates' => $coordinates->serialize(),
                'orientation' => $orientation->toString()
            ];

            $index++;
        }

        return [
            'data' => $routeData,
            'hasObstacle' => $hasObstacle,
            'obstacleIndex' => $hasObstacle ? $obstacleIndex : -1
        ];
    }

    protected function findAltRoutes(
        Terrain        $terrain,
        Coordinates    $coordinates,
        Orientation    $orientation,
        ComplexCommand $command,
        array          $simulatedRouteData
    ): array
    {
        $altCommands = [
            self::TO_THE_RIGHT_COMPLEX_COMMAND
        ];

        $altRoutes = [];
        foreach ($altCommands as $altCommand) {
            $newCommand = $command
                ->removeAt($simulatedRouteData['obstacleIndex'])
                ->removeAt($simulatedRouteData['obstacleIndex'] + 1)
                ->insertAt($simulatedRouteData['obstacleIndex'], $altCommand);

            $altRouteData = $this->routeDataFromComplexCommand(
                $terrain,
                $coordinates,
                $orientation,
                $newCommand
            );

            if (!$altRouteData['hasObstacle']) {
                $altRoutes[] = Route::fromArray($altRouteData['data']);
            }
        }

        return $altRoutes;
    }

}