<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Domain\MarsRover;

use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\RepairResult;
use PHPUnit\Framework\TestCase;

class RepairResultTest extends TestCase
{
    public function test_it_should_create_ok_result(): void
    {
        $result = RepairResult::ok();

        $this->assertTrue($result->isOk());
        $this->assertNull($result->failure());
    }

    public function test_it_should_create_ko_result(): void
    {
        $result = RepairResult::ko('failure');

        $this->assertFalse($result->isOk());
        $this->assertEquals('failure', $result->failure());
    }
}