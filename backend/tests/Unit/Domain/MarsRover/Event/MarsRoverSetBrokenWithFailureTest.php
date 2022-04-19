<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover\Event;

use MarsRoverKata\Domain\MarsRover\Event\MarsRoverSetBrokenWithFailure;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class MarsRoverSetBrokenWithFailureTest extends TestCase
{
    public function test_it_should_serialize_deserialize_event(): void
    {
        $id = Uuid::uuid4();
        $failure = 'failure';
        $nowDate = new \DateTimeImmutable();
        $now = $nowDate->format(DATE_ISO8601);
        $maintenanceDate = new \DateTimeImmutable($now);

        $marsRoverSetBrokenWithFailure = new MarsRoverSetBrokenWithFailure(
            $id,
            $failure,
            $maintenanceDate
        );

        $expectedSerializedEvent = [
            'id' => $id->toString(),
            'failure' => $failure,
            'maintenanceDate' => $maintenanceDate->format(DATE_ISO8601),
        ];

        $this->assertEquals($expectedSerializedEvent, $marsRoverSetBrokenWithFailure->serialize());
        $this->assertEquals($marsRoverSetBrokenWithFailure, MarsRoverSetBrokenWithFailure::deserialize($expectedSerializedEvent));
    }
}