<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover\Event;

use MarsRoverKata\Domain\MarsRover\Event\ObstacleDetected;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ObstacleDetectedTest extends TestCase
{
    public function test_it_should_serialize_deserialize_event(): void
    {
        $id = Uuid::uuid4();

        $obstacleDetected = new ObstacleDetected(
            $id
        );

        $expectedSerializedEvent = [
            'id' => $id->toString()
        ];

        $this->assertEquals($expectedSerializedEvent, $obstacleDetected->serialize());
        $this->assertEquals($obstacleDetected, ObstacleDetected::deserialize($expectedSerializedEvent));
    }
}