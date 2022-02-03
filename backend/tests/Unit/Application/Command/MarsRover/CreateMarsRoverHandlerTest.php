<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Command\MarsRover;

use MarsRoverKata\Application\Command\MarsRover\CreateMarsRover;
use MarsRoverKata\Application\Command\MarsRover\CreateMarsRoverHandler;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use MarsRoverKata\Domain\MarsRover\Terrain;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CreateMarsRoverHandlerTest extends TestCase
{
    public function test_it_should_handle_create_mars_rover_command(): void
    {
        $marsRoverRepository = $this->prophesize(MarsRoverRepository::class);

        $createMarsRoverHandler = new CreateMarsRoverHandler(
            $marsRoverRepository->reveal()
        );

        $createMarsRoverHandler(
            new CreateMarsRover(
                Uuid::uuid4(),
                'test-rover',
                Terrain::default(),
                new \DateTimeImmutable()
            )
        );
    }
}