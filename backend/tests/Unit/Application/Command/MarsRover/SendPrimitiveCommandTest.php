<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Command\MarsRover;

use MarsRoverKata\Application\Command\MarsRover\SendPrimitiveCommand;
use MarsRoverKata\Domain\MarsRover\PrimitiveCommand;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class SendPrimitiveCommandTest extends TestCase
{
    public function test_it_should_send_primitive_command_to_mars_rover(): void
    {
        $id = Uuid::uuid4();
        $primitiveCommand = PrimitiveCommand::fromString('F');
        $command = new SendPrimitiveCommand(
            $id,
            $primitiveCommand
        );

        $this->assertEquals($id->toString(), $command->getId()->toString());
        $this->assertEquals($primitiveCommand->toString(), $command->getPrimitiveCommand()->toString());
    }
}