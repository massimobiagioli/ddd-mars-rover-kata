<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover\Event;

use MarsRoverKata\Domain\MarsRover\Event\MaintenanceLightTurnedOn;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class MaintenanceLightTurnedOnTest extends TestCase
{
    public function test_it_should_serialize_deserialize_event(): void
    {
        $id = Uuid::uuid4();
        $km = 123;

        $maintenanceLightTurnedOn = new MaintenanceLightTurnedOn(
            $id,
            $km
        );

        $expectedSerializedEvent = [
            'id' => $id->toString(),
            'km' => 123
        ];

        $this->assertEquals($expectedSerializedEvent, $maintenanceLightTurnedOn->serialize());
        $this->assertEquals($maintenanceLightTurnedOn, MaintenanceLightTurnedOn::deserialize($expectedSerializedEvent));
    }
}