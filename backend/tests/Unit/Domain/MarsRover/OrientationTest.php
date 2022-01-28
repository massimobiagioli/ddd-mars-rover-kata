<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover;

use MarsRoverKata\Domain\MarsRover\Orientation;
use PHPUnit\Framework\TestCase;

class OrientationTest extends TestCase
{
    public function test_it_should_throw_exception_if_wrong_width_provided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value X is not valid for orientation');

        Orientation::fromString('X');
    }

    public function test_it_should_create_orientation(): void
    {
        $orientation = Orientation::fromString('N');

        $this->assertEquals('N', $orientation->toString());
    }
}