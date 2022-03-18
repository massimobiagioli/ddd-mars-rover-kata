<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover\Event;

use MarsRoverKata\Domain\MarsRover\Event\MarsRoverPaused;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class MarsRoverPausedTest extends TestCase
{
    public function test_it_should_serialize_deserialize_event(): void
    {
        $id = Uuid::uuid4();

        $marsRoverPaused = new MarsRoverPaused(
            $id
        );

        $expectedSerializedEvent = [
            'id' => $id->toString()
        ];

        $this->assertEquals($expectedSerializedEvent, $marsRoverPaused->serialize());
        $this->assertEquals($marsRoverPaused, MarsRoverPaused::deserialize($expectedSerializedEvent));
    }
}