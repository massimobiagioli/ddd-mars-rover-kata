<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover\Event;

use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverCreated;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverPlaced;
use MarsRoverKata\Domain\MarsRover\Orientation;
use MarsRoverKata\Domain\MarsRover\Terrain;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class MarsRoverPlacedTest extends TestCase
{
    public function test_it_should_serialize_deserialize_event(): void
    {
        $id = Uuid::uuid4();
        $coordinates = Coordinates::create(1, 2);
        $orientation = Orientation::fromString('N');

        $marsRoverPlaced = new MarsRoverPlaced(
            $id,
            $coordinates,
            $orientation
        );

        $expectedSerializedEvent = [
            'id' => $id->toString(),
            'coordinates' => [
                'x' => 1,
                'y' => 2
            ],
            'orientation' => 'N'
        ];

        $this->assertEquals($expectedSerializedEvent, $marsRoverPlaced->serialize());
        $this->assertEquals($marsRoverPlaced, MarsRoverPlaced::deserialize($expectedSerializedEvent));
    }
}