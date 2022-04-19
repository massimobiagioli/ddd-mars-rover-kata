<?php
declare(strict_types=1);

namespace MarsRover\Tests\Unit\Domain\Terrain;

use MarsRoverKata\Domain\MarsRover\Terrain;
use PHPUnit\Framework\TestCase;

class TerrainTest extends TestCase
{
    public function test_it_should_throw_exception_if_wrong_height_provided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Height must be at least 3');

        Terrain::create(2, 8);
    }

    public function test_it_should_throw_exception_if_wrong_width_provided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Width must be at least 3');

        Terrain::create(3, 1);
    }

    public function test_it_should_create_terrain(): void
    {
        $terrain = Terrain::create(5, 8);

        $this->assertEquals(["height" => 5, "width" => 8], $terrain->serialize());
    }

    public function test_it_should_create_terrain_from_array(): void
    {
        $data = ["height" => 5, "width" => 8];
        $terrain = Terrain::fromArray($data);

        $this->assertEquals($data, $terrain->serialize());
    }

    public function test_it_should_create_default_terrain(): void
    {
        $terrain = Terrain::default();

        $this->assertEquals(["height" => 20, "width" => 20], $terrain->serialize());
    }

    public function test_it_should_cap_coordinates(): void
    {
        $terrain = Terrain::create(5, 5);
        $negativeX = $terrain->capX(-1);
        $overflowX = $terrain->capX(6);
        $rightX = $terrain->capX(3);
        $negativeY = $terrain->capY(-2);
        $overflowY = $terrain->capY(8);
        $rightY = $terrain->capY(5);

        $this->assertEquals(0, $negativeX);
        $this->assertEquals(5, $overflowX);
        $this->assertEquals(3, $rightX);
        $this->assertEquals(0, $negativeY);
        $this->assertEquals(5, $overflowY);
        $this->assertEquals(5, $rightY);
    }
}
