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

        $marsRoverSetBrokenWithFailure = new MarsRoverSetBrokenWithFailure(
            $id,
            $failure
        );

        $expectedSerializedEvent = [
            'id' => $id->toString(),
            'failure' => $failure,
        ];

        $this->assertEquals($expectedSerializedEvent, $marsRoverSetBrokenWithFailure->serialize());
        $this->assertEquals($marsRoverSetBrokenWithFailure, MarsRoverSetBrokenWithFailure::deserialize($expectedSerializedEvent));
    }
}