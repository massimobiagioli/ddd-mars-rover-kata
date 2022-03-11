<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Command\MarsRover;

use MarsRoverKata\Application\Command\MarsRover\SendComplexCommand;
use MarsRoverKata\Domain\MarsRover\ComplexCommand;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class SendComplexCommandTest extends TestCase
{
    public function test_it_should_send_complex_command_to_mars_rover(): void
    {
        $id = Uuid::uuid4();
        $complexCommand = ComplexCommand::fromString('FFLF');
        $command = new SendComplexCommand(
            $id,
            $complexCommand
        );

        $this->assertEquals($id->toString(), $command->getId()->toString());
        $this->assertEquals($complexCommand->toString(), $command->getComplexCommand()->toString());
    }
}