<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover\Event;

use MarsRoverKata\Domain\MarsRover\Event\PrimitiveCommandSent;
use MarsRoverKata\Domain\MarsRover\PrimitiveCommand;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class PrimitiveCommandSentTest extends TestCase
{
    public function test_it_should_serialize_deserialize_event(): void
    {
        $id = Uuid::uuid4();
        $primitiveCommand = PrimitiveCommand::fromString('F');

        $primitiveCommandSent = new PrimitiveCommandSent(
            $id,
            $primitiveCommand
        );

        $expectedSerializedEvent = [
            'id' => $id->toString(),
            'primitive_command' => 'F'
        ];

        $this->assertEquals($expectedSerializedEvent, $primitiveCommandSent->serialize());
        $this->assertEquals($primitiveCommandSent, PrimitiveCommandSent::deserialize($expectedSerializedEvent));
    }
}