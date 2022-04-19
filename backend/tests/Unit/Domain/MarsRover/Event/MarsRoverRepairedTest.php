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

        $marsRoverRepaired = new MarsRoverRepaired(
            $id
        );

        $expectedSerializedEvent = [
            'id' => $id->toString()
        ];

        $this->assertEquals($expectedSerializedEvent, $marsRoverRepaired->serialize());
        $this->assertEquals($marsRoverRepaired, MarsRoverRepaired::deserialize($expectedSerializedEvent));
    }
}