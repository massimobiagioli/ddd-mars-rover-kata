<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover;

use MarsRoverKata\Domain\MarsRover\ComplexCommand;
use PHPUnit\Framework\TestCase;

class ComplexCommandTest extends TestCase
{
    public function test_it_should_throw_exception_if_wrong_commands_provided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value X is not valid for complex command');

        ComplexCommand::fromString('FX');
    }

    public function test_it_should_create_complex_command(): void
    {
        $complexCommand = ComplexCommand::fromString('FFLF');

        $this->assertEquals('FFLF', $complexCommand->toString());
        $this->assertCount(4, $complexCommand->getPrimitiveCommands());
        $this->assertEquals('F', $complexCommand->getPrimitiveCommands()[0]->toString());
        $this->assertEquals('F', $complexCommand->getPrimitiveCommands()[1]->toString());
        $this->assertEquals('L', $complexCommand->getPrimitiveCommands()[2]->toString());
        $this->assertEquals('F', $complexCommand->getPrimitiveCommands()[3]->toString());
    }

    public function test_it_should_insert_at_position(): void
    {
        $complexCommand = ComplexCommand::fromString('FRF')->insertAt(1, 'LFF');

        $this->assertEquals('FLFFRF', $complexCommand->toString());
        $this->assertCount(6, $complexCommand->getPrimitiveCommands());
        $this->assertEquals('F', $complexCommand->getPrimitiveCommands()[0]->toString());
        $this->assertEquals('L', $complexCommand->getPrimitiveCommands()[1]->toString());
        $this->assertEquals('F', $complexCommand->getPrimitiveCommands()[2]->toString());
        $this->assertEquals('F', $complexCommand->getPrimitiveCommands()[3]->toString());
        $this->assertEquals('R', $complexCommand->getPrimitiveCommands()[4]->toString());
        $this->assertEquals('F', $complexCommand->getPrimitiveCommands()[5]->toString());
    }

    public function test_it_should_remove_at_position(): void
    {
        $complexCommand = ComplexCommand::fromString('FRF')->removeAt(1);

        $this->assertEquals('FF', $complexCommand->toString());
        $this->assertCount(2, $complexCommand->getPrimitiveCommands());
        $this->assertEquals('F', $complexCommand->getPrimitiveCommands()[0]->toString());
        $this->assertEquals('F', $complexCommand->getPrimitiveCommands()[1]->toString());
    }

    public function test_it_should_insert_and_remove_at_position(): void
    {
        $complexCommand = ComplexCommand::fromString('FRF')
            ->removeAt(1)
            ->insertAt(1, 'LLF');

        $this->assertEquals('FLLFF', $complexCommand->toString());
        $this->assertCount(5, $complexCommand->getPrimitiveCommands());
        $this->assertEquals('F', $complexCommand->getPrimitiveCommands()[0]->toString());
        $this->assertEquals('L', $complexCommand->getPrimitiveCommands()[1]->toString());
        $this->assertEquals('L', $complexCommand->getPrimitiveCommands()[2]->toString());
        $this->assertEquals('F', $complexCommand->getPrimitiveCommands()[3]->toString());
        $this->assertEquals('F', $complexCommand->getPrimitiveCommands()[4]->toString());
    }
}