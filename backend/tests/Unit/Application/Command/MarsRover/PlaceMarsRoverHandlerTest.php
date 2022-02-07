<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Command\MarsRover;

use H2P\Domain\Booking\BookingId;
use H2P\Infrastructure\Timify\BookingDataDTO;
use MarsRoverKata\Application\Command\MarsRover\PlaceMarsRover;
use MarsRoverKata\Application\Command\MarsRover\PlaceMarsRoverHandler;
use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\MarsRover;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use MarsRoverKata\Domain\MarsRover\Orientation;
use MarsRoverKata\Domain\MarsRover\Terrain;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class PlaceMarsRoverHandlerTest extends TestCase
{
    public function test_it_should_throw_exception_if_mars_rover_does_not_exists(): void
    {
        $id = Uuid::fromString('00000000-0000-0000-0000-000000000000');

        $marsRoverRepository = $this->prophesize(MarsRoverRepository::class);
        $marsRoverRepository
            ->get($id)
            ->willReturn(null);

        $logger = $this->prophesize(LoggerInterface::class);

        $placeMarsRoverHandler = new PlaceMarsRoverHandler(
            $marsRoverRepository->reveal(),
            $logger->reveal()
        );

        $placeMarsRoverHandler(
            new PlaceMarsRover(
                $id,
                Coordinates::create(1, 2),
                Orientation::fromString('S')
            )
        );

        $logger
            ->critical("Mars Rover with id: 00000000-0000-0000-0000-000000000000 not found!!!")
            ->shouldHaveBeenCalledTimes(1);
    }

    public function test_it_should_place_mars_rover(): void
    {
        $id = Uuid::fromString('938e3660-5226-4fdd-b080-216639741141');

        $marsRover = MarsRover::create(
            $id,
            'test-rover',
            Terrain::default(),
            new \DateTimeImmutable()
        );

        $marsRoverRepository = $this->prophesize(MarsRoverRepository::class);
        $marsRoverRepository
            ->get($id)
            ->willReturn($marsRover);

        $logger = $this->prophesize(LoggerInterface::class);

        $placeMarsRoverHandler = new PlaceMarsRoverHandler(
            $marsRoverRepository->reveal(),
            $logger->reveal()
        );

        $placeMarsRoverHandler(
            new PlaceMarsRover(
                $id,
                Coordinates::create(1, 2),
                Orientation::fromString('S')
            )
        );

        $marsRoverRepository
            ->store($marsRover)
            ->shouldHaveBeenCalledTimes(1);
    }
}