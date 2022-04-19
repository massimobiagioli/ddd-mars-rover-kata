<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover\Event;

use MarsRoverKata\Domain\MarsRover\Event\MarsRoverRepaired;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class MarsRoverRepairedTest extends TestCase
{
    public function test_it_should_serialize_deserialize_event(): void
    {
        $id = Uuid::uuid4();
        $nowDate = new \DateTimeImmutable();
        $now = $nowDate->format(DATE_ISO8601);
        $maintenanceDate = new \DateTimeImmutable($now);

        $marsRoverRepaired = new MarsRoverRepaired(
            $id,
            $maintenanceDate
        );

        $expectedSerializedEvent = [
            'id' => $id->toString(),
            'maintenanceDate' => $maintenanceDate->format(DATE_ISO8601),
        ];

        $this->assertEquals($expectedSerializedEvent, $marsRoverRepaired->serialize());
        $this->assertEquals($marsRoverRepaired, MarsRoverRepaired::deserialize($expectedSerializedEvent));
    }
}