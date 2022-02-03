<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover\Event;

use MarsRoverKata\Domain\MarsRover\Event\MarsRoverCreated;
use MarsRoverKata\Domain\MarsRover\Terrain;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class MarsRoverCreatedTest extends TestCase
{
    public function test_it_should_serialize_deserialize_event(): void
    {
        $id = Uuid::uuid4();
        $name = 'rover-1';
        $terrain = Terrain::default();
        $nowDate = new \DateTimeImmutable();
        $now = $nowDate->format(DATE_ISO8601);
        $createdAt = new \DateTimeImmutable($now);

        $marsRoverCreated = new MarsRoverCreated(
            $id,
            $name,
            $terrain,
            $createdAt
        );

        $expectedSerializedEvent = [
            'id' => $id->toString(),
            'name' => 'rover-1',
            'terrain' => [
                'height' => 10,
                'width' => 10
            ],
            'createdAt' => $createdAt->format(DATE_ISO8601),
        ];

        $this->assertEquals($expectedSerializedEvent, $marsRoverCreated->serialize());
        $this->assertEquals($marsRoverCreated, MarsRoverCreated::deserialize($expectedSerializedEvent));
    }
}