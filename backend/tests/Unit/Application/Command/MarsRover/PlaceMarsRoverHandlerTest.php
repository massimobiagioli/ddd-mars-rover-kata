<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Command\MarsRover;

use Broadway\Domain\DomainMessage;
use H2P\Domain\Booking\BookingId;
use H2P\Infrastructure\Timify\BookingDataDTO;
use MarsRoverKata\Application\Command\MarsRover\PlaceMarsRover;
use MarsRoverKata\Application\Command\MarsRover\PlaceMarsRoverHandler;
use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverPlaced;
use MarsRoverKata\Domain\MarsRover\MarsRover;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use MarsRoverKata\Domain\MarsRover\Orientation;
use MarsRoverKata\Domain\MarsRover\Terrain;
use MarsRoverKata\Tests\Fixtures\MarsRover\MarsRoverBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
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
        $id = Uuid::uuid4();
        $name = 'test-rover';
        $terrain = Terrain::default();
        $createdAt = new \DateTimeImmutable();

        $marsRoverBuilder = new MarsRoverBuilder();
        $marsRover = $marsRoverBuilder
            ->withId($id)
            ->withName($name)
            ->withTerrain($terrain)
            ->withCreatedAt($createdAt)
            ->get();

        $marsRoverRepository = $this->prophesize(MarsRoverRepository::class);
        $marsRoverRepository
            ->get($id)
            ->willReturn($marsRover);

        $marsRover->getUncommittedEvents();

        $logger = $this->prophesize(LoggerInterface::class);

        $coordinates = Coordinates::create(2, 3);
        $orientation = Orientation::fromString('N');
        $placeMarsRoverCommand = new PlaceMarsRover(
            $id,
            $coordinates,
            $orientation
        );

        $placeMarsRoverCommandHandler = new PlaceMarsRoverHandler(
            $marsRoverRepository->reveal(),
            $logger->reveal()
        );

        $placeMarsRoverCommandHandler($placeMarsRoverCommand);

        $domainEventStream = $marsRover->getUncommittedEvents();

        $expected = [
            new MarsRoverPlaced(
                $id,
                $coordinates,
                $orientation
            )
        ];

        $events = iterator_to_array($domainEventStream->getIterator());

        $events = array_map(function (DomainMessage $event): object {
            return $event->getPayload();
        }, array_values($events));

        $this->assertEquals($expected, $events);

        $marsRoverRepository->store(Argument::type(MarsRover::class))->shouldHaveBeenCalled();
    }

    public function test_it_should_skip_command_if_mars_rover_is_paused(): void
    {
        $id = Uuid::uuid4();
        $name = 'test-rover';
        $terrain = Terrain::default();
        $createdAt = new \DateTimeImmutable();

        $marsRoverBuilder = new MarsRoverBuilder();
        $marsRover = $marsRoverBuilder
            ->withId($id)
            ->withName($name)
            ->withTerrain($terrain)
            ->withCreatedAt($createdAt)
            ->get();

        $marsRover->pause();

        $marsRoverRepository = $this->prophesize(MarsRoverRepository::class);
        $marsRoverRepository
            ->get($id)
            ->willReturn($marsRover);

        $marsRover->getUncommittedEvents();

        $logger = $this->prophesize(LoggerInterface::class);

        $coordinates = Coordinates::create(2, 3);
        $orientation = Orientation::fromString('N');
        $placeMarsRoverCommand = new PlaceMarsRover(
            $id,
            $coordinates,
            $orientation
        );

        $placeMarsRoverCommandHandler = new PlaceMarsRoverHandler(
            $marsRoverRepository->reveal(),
            $logger->reveal()
        );

        $placeMarsRoverCommandHandler($placeMarsRoverCommand);

        $domainEventStream = $marsRover->getUncommittedEvents();
        $events = iterator_to_array($domainEventStream->getIterator());

        $this->assertCount(0, $events);

        $marsRoverRepository->store(Argument::type(MarsRover::class))->shouldNotHaveBeenCalled();
    }
}