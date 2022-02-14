<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover;

use MarsRoverKata\Domain\MarsRover\PrimitiveCommand;
use PHPUnit\Framework\TestCase;

class PrimitiveCommandTest extends TestCase
{
    public function test_it_should_throw_exception_if_wrong_command_provided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value X is not valid for primitive command');

        PrimitiveCommand::fromString('X');
    }

    public function test_it_should_create_forward_primitive_command(): void
    {
        $primitiveCommand = PrimitiveCommand::fromString('F');

        $this->assertEquals('F', $primitiveCommand->toString());
    }
}