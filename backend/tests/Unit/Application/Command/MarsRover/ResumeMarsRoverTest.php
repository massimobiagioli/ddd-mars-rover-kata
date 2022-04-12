<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Command\MarsRover;

use MarsRoverKata\Application\Command\MarsRover\ResumeMarsRover;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ResumeMarsRoverTest extends TestCase
{
    public function test_it_should_create_new_resume_mars_rover_command(): void
    {
        $id = Uuid::uuid4();
        $command = new ResumeMarsRover($id);

        $this->assertEquals($id->toString(), $command->getId()->toString());
    }
}