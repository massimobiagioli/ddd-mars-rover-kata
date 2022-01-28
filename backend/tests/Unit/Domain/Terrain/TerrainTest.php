<?php
declare(strict_types=1);

namespace MarsRover\Tests\Unit\Domain\Terrain;

use MarsRoverKata\Domain\Terrain\Terrain;
use PHPUnit\Framework\TestCase;

class TerrainTest extends TestCase
{
    public function test_it_should_create_terrain(): void
    {
        $terrain = Terrain::create(5, 8);

        $this->assertEquals(5, $terrain->height());
        $this->assertEquals(8, $terrain->width());
    }

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
}
