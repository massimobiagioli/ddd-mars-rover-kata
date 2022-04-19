<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Command\MarsRover;

use Broadway\Domain\DomainMessage;
use H2P\Domain\Booking\BookingId;
use H2P\Infrastructure\Timify\BookingDataDTO;
use MarsRoverKata\Application\Command\MarsRover\RepairMarsRover;
use MarsRoverKata\Application\Command\MarsRover\RepairMarsRoverHandler;
use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverRepaired;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverSetBrokenWithFailure;
use MarsRoverKata\Domain\MarsRover\MarsRover;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use MarsRoverKata\Domain\MarsRover\Orientation;
use MarsRoverKata\Domain\MarsRover\PrimitiveCommand;
use MarsRoverKata\Domain\MarsRover\RepairResult;
use MarsRoverKata\Domain\MarsRover\Terrain;
use MarsRoverKata\Tests\Fixtures\MarsRover\MarsRoverBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class RepairMarsRoverHandlerTest extends TestCase
{
    public function test_it_should_throw_exception_if_mars_rover_does_not_exists(): void
    {
        $id = Uuid::fromString('00000000-0000-0000-0000-000000000000');
        $createdAt = new \DateTimeImmutable();

        $marsRoverRepository = $this->prophesize(MarsRoverRepository::class);
        $marsRoverRepository
            ->get($id)
            ->willReturn(null);

        $logger = $this->prophesize(LoggerInterface::class);

        $repairMarsRoverHandler = new RepairMarsRoverHandler(
            $marsRoverRepository->reveal(),
            $logger->reveal()
        );

        $repairMarsRoverHandler(
            new RepairMarsRover(
                $id,
                RepairResult::ok(),
                $createdAt
            )
        );

        $logger
            ->critical("Mars Rover with id: 00000000-0000-0000-0000-000000000000 not found!!!")
            ->shouldHaveBeenCalledTimes(1);
    }

    public function test_it_should_not_repair_a_mars_rover_with_maintenance_light_turned_off(): void
    {
        $id = Uuid::uuid4();
        $name = 'test-rover';
        $terrain = Terrain::create(20, 20);
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

        $logger = $this->prophesize(LoggerInterface::class);

        $marsRover->place(
            Coordinates::create(1, 3),
            Orientation::fromString('W')
        );

        $marsRover->getUncommittedEvents();

        $repairMarsRover = new RepairMarsRover(
            $id,
            RepairResult::ok(),
            $createdAt
        );
        $repairMarsRoverCommandHandler = new RepairMarsRoverHandler(
            $marsRoverRepository->reveal(),
            $logger->reveal()
        );
        $repairMarsRoverCommandHandler($repairMarsRover);

        $domainEventStream = $marsRover->getUncommittedEvents();

        $expected = [];

        $events = iterator_to_array($domainEventStream->getIterator());

        $events = array_map(function (DomainMessage $event): object {
            return $event->getPayload();
        }, array_values($events));

        $this->assertEquals($expected, $events);

        $marsRoverRepository->store(Argument::type(MarsRover::class))->shouldNotHaveBeenCalled();
    }

    public function test_it_should_repair_a_mars_rover_with_maintenance_light_turned_on(): void
    {
        $id = Uuid::uuid4();
        $name = 'test-rover';
        $terrain = Terrain::create(20, 20);
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

        $logger = $this->prophesize(LoggerInterface::class);

        $marsRover->place(
            Coordinates::create(1, 3),
            Orientation::fromString('W')
        );

        for ($i = 0; $i < 12; $i++) {
            $marsRover->sendCommand(
                PrimitiveCommand::fromString('F')
            );
        }

        $marsRover->getUncommittedEvents();

        $repairMarsRover = new RepairMarsRover(
            $id,
            RepairResult::ok(),
            $createdAt
        );
        $repairMarsRoverCommandHandler = new RepairMarsRoverHandler(
            $marsRoverRepository->reveal(),
            $logger->reveal()
        );
        $repairMarsRoverCommandHandler($repairMarsRover);

        $domainEventStream = $marsRover->getUncommittedEvents();

        $expected = [
            new MarsRoverRepaired(
                $id
            ),
        ];

        $events = iterator_to_array($domainEventStream->getIterator());

        $events = array_map(function (DomainMessage $event): object {
            return $event->getPayload();
        }, array_values($events));

        $this->assertEquals($expected, $events);

        $marsRoverRepository->store(Argument::type(MarsRover::class))->shouldHaveBeenCalled();
    }

    public function test_it_should_set_a_mars_rover_broken_with_failure_with_maintenance_light_turned_on(): void
    {
        $id = Uuid::uuid4();
        $name = 'test-rover';
        $terrain = Terrain::create(20, 20);
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

        $logger = $this->prophesize(LoggerInterface::class);

        $marsRover->place(
            Coordinates::create(1, 3),
            Orientation::fromString('W')
        );

        for ($i = 0; $i < 12; $i++) {
            $marsRover->sendCommand(
                PrimitiveCommand::fromString('F')
            );
        }

        $marsRover->getUncommittedEvents();

        $failure = 'failure message';

        $repairMarsRover = new RepairMarsRover(
            $id,
            RepairResult::ko($failure),
            $createdAt
        );
        $repairMarsRoverCommandHandler = new RepairMarsRoverHandler(
            $marsRoverRepository->reveal(),
            $logger->reveal()
        );
        $repairMarsRoverCommandHandler($repairMarsRover);

        $domainEventStream = $marsRover->getUncommittedEvents();

        $expected = [
            new MarsRoverSetBrokenWithFailure(
                $id,
                $failure
            ),
        ];

        $events = iterator_to_array($domainEventStream->getIterator());

        $events = array_map(function (DomainMessage $event): object {
            return $event->getPayload();
        }, array_values($events));

        $this->assertEquals($expected, $events);

        $marsRoverRepository->store(Argument::type(MarsRover::class))->shouldHaveBeenCalled();
    }
}