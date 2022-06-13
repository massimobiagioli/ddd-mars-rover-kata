<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover\Event;

use MarsRoverKata\Domain\MarsRover\Event\ObstacleDetected;
use MarsRoverKata\Domain\MarsRover\Route\Route;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ObstacleDetectedTest extends TestCase
{
    public function test_it_should_serialize_deserialize_event(): void
    {
        $id = Uuid::uuid4();

        $routeData = [
            ['coordinates' => ["x" => 1, "y" => 2], 'orientation' => 'N'],
            ['coordinates' => ["x" => 2, "y" => 3], 'orientation' => 'E'],
        ];
        $route = Route::fromArray($routeData);

        $obstacleDetected = new ObstacleDetected(
            $id,
            $route
        );

        $expectedSerializedEvent = [
            'id' => $id->toString(),
            'route' => $routeData
        ];

        $this->assertEquals($expectedSerializedEvent, $obstacleDetected->serialize());
        $this->assertEquals($obstacleDetected, ObstacleDetected::deserialize($expectedSerializedEvent));
    }
}