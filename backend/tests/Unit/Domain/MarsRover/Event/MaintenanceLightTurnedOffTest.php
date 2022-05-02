<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover\Event;

use MarsRoverKata\Domain\MarsRover\Event\MaintenanceLightTurnedOff;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class MaintenanceLightTurnedOffTest extends TestCase
{
    public function test_it_should_serialize_deserialize_event(): void
    {
        $id = Uuid::uuid4();

        $maintenanceLightTurnedOff = new MaintenanceLightTurnedOff(
            $id
        );

        $expectedSerializedEvent = [
            'id' => $id->toString()
        ];

        $this->assertEquals($expectedSerializedEvent, $maintenanceLightTurnedOff->serialize());
        $this->assertEquals($maintenanceLightTurnedOff, MaintenanceLightTurnedOff::deserialize($expectedSerializedEvent));
    }
}