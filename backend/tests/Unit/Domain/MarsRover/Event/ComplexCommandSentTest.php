<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover\Event;

use MarsRoverKata\Domain\MarsRover\ComplexCommand;
use MarsRoverKata\Domain\MarsRover\Event\ComplexCommandSent;
use MarsRoverKata\Domain\MarsRover\Event\PrimitiveCommandSent;
use MarsRoverKata\Domain\MarsRover\PrimitiveCommand;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ComplexCommandSentTest extends TestCase
{
    public function test_it_should_serialize_deserialize_event(): void
    {
        $id = Uuid::uuid4();
        $complexCommand = ComplexCommand::fromString('FFRF');

        $complexCommandSent = new ComplexCommandSent(
            $id,
            $complexCommand
        );

        $expectedSerializedEvent = [
            'id' => $id->toString(),
            'complex_command' => 'FFRF'
        ];

        $this->assertEquals($expectedSerializedEvent, $complexCommandSent->serialize());
        $this->assertEquals($complexCommandSent, ComplexCommandSent::deserialize($expectedSerializedEvent));
    }
}