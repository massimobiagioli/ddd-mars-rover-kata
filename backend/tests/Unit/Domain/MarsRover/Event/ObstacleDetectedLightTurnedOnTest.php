<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover\Event;

use MarsRoverKata\Domain\MarsRover\Event\ObstacleDetectedLightTurnedOn;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ObstacleDetectedLightTurnedOnTest extends TestCase
{
    public function test_it_should_serialize_deserialize_event(): void
    {
        $id = Uuid::uuid4();

        $obstacleDetectedLightTurnedOn = new ObstacleDetectedLightTurnedOn(
            $id
        );

        $expectedSerializedEvent = [
            'id' => $id->toString()
        ];

        $this->assertEquals($expectedSerializedEvent, $obstacleDetectedLightTurnedOn->serialize());
        $this->assertEquals($obstacleDetectedLightTurnedOn, ObstacleDetectedLightTurnedOn::deserialize($expectedSerializedEvent));
    }
}