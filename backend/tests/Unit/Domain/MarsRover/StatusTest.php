<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover;

use MarsRoverKata\Domain\MarsRover\Orientation;
use MarsRoverKata\Domain\MarsRover\Status;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    public function test_it_should_create_status(): void
    {
        $statusCreated = Status::created();
        $statusPlaced = Status::placed();

        $this->assertEquals('created', $statusCreated->toString());
        $this->assertEquals('placed', $statusPlaced->toString());
    }
}