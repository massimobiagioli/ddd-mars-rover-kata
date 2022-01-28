<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover;

use MarsRoverKata\Domain\MarsRover\Coordinates;
use PHPUnit\Framework\TestCase;

class CoordinatesTest extends TestCase
{
    public function test_it_should_throw_exception_if_wrong_x_provided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('X must be greater or equals to 0');

        Coordinates::create(-1, 1);
    }

    public function test_it_should_throw_exception_if_wrong_y_provided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Y must be greater or equals to 0');

        Coordinates::create(1, -1);
    }

    public function test_it_should_create_coordinates(): void
    {
        $coordinates = Coordinates::create(1, 2);

        $this->assertEquals(["x" => 1, "y" => 2], $coordinates->serialize());
    }
}