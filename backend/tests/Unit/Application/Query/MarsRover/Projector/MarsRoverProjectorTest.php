<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Query\MarsRover\Projector;

use MarsRoverKata\Application\Query\MarsRover\MarsRover;
use MarsRoverKata\Application\Query\MarsRover\MarsRoverRepository;
use MarsRoverKata\Application\Query\MarsRover\Projector\MarsRoverProjector;
use MarsRoverKata\Domain\MarsRover\ComplexCommand;
use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Event\ComplexCommandSent;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverCreated;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverPlaced;
use MarsRoverKata\Domain\MarsRover\Event\PrimitiveCommandSent;
use MarsRoverKata\Domain\MarsRover\Orientation;
use MarsRoverKata\Domain\MarsRover\PrimitiveCommand;
use MarsRoverKata\Domain\MarsRover\Status;
use MarsRoverKata\Domain\MarsRover\Terrain;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class MarsRoverProjectorTest extends TestCase
{
    public function test_it_should_handle_mars_rover_create_event(): void
    {
        $marsRoverRepository = $this->prophesize(MarsRoverRepository::class);
        $loggerInterface = $this->prophesize(LoggerInterface::class);

        $marsRoverProjector = new MarsRoverProjector(
            $marsRoverRepository->reveal(),
            $loggerInterface->reveal()
        );

        $id = Uuid::uuid4();
        $name = 'test-rover';
        $terrain = Terrain::default();
        $createdAt = new \DateTimeImmutable();

        $marsRover = new MarsRover(
            $id->toString(),
            $name,
            \DateTime::createFromImmutable($createdAt)
        );

        $marsRoverCreatedEvent = new MarsRoverCreated(
            $id,
            $name,
            $terrain,
            $createdAt
        );

        $marsRoverProjector->applyMarsRoverCreated($marsRoverCreatedEvent);

        $marsRoverRepository
            ->store($marsRover->withStatus(Status::created()))
            ->shouldHaveBeenCalledTimes(1);
    }

    public function test_it_should_handle_mars_rover_place_event_when_mars_rover_not_exists(): void
    {
        $marsRoverRepository = $this->prophesize(MarsRoverRepository::class);
        $logger = $this->prophesize(LoggerInterface::class);

        $marsRoverProjector = new MarsRoverProjector(
            $marsRoverRepository->reveal(),
            $logger->reveal()
        );

        $id = Uuid::fromString("00000000-0000-0000-0000-000000000000");
        $coordinates = Coordinates::create(1, 2);
        $orientation = Orientation::fromString('N');

        $marsRoverPlacedEvent = new MarsRoverPlaced(
            $id,
            $coordinates,
            $orientation
        );

        $marsRoverProjector->applyMarsRoverPlaced($marsRoverPlacedEvent);

        $logger
            ->critical("Mars Rover with id: 00000000-0000-0000-0000-000000000000 not found!!!")
            ->shouldHaveBeenCalledTimes(1);

        $marsRoverRepository
            ->store(Argument::type(MarsRover::class))
            ->shouldHaveBeenCalledTimes(0);
    }

    public function test_it_should_handle_mars_rover_place_event(): void
    {
        $id = Uuid::uuid4();
        $name = 'test-rover';
        $createdAt = new \DateTime();

        $marsRover = new MarsRover(
            $id->toString(),
            $name,
            $createdAt
        );

        $marsRoverRepository = $this->prophesize(MarsRoverRepository::class);
        $marsRoverRepository
            ->get($id->toString())
            ->willReturn($marsRover);

        $loggerInterface = $this->prophesize(LoggerInterface::class);

        $marsRoverProjector = new MarsRoverProjector(
            $marsRoverRepository->reveal(),
            $loggerInterface->reveal()
        );

        $coordinates = Coordinates::create(1, 2);
        $orientation = Orientation::fromString('N');

        $marsRoverPlacedEvent = new MarsRoverPlaced(
            $id,
            $coordinates,
            $orientation
        );

        $marsRoverProjector->applyMarsRoverPlaced($marsRoverPlacedEvent);

        $marsRoverRepository
            ->store($marsRover
                ->withCoordinates($marsRoverPlacedEvent->getCoordinates())
                ->withOrientation($marsRoverPlacedEvent->getOrientation())
                ->withStatus(Status::placed())
            )
            ->shouldHaveBeenCalledTimes(1);
    }

    public function test_it_should_handle_mars_rover_primitive_command_sent_event_when_primitive_command_not_update_km(): void
    {
        $marsRoverRepository = $this->prophesize(MarsRoverRepository::class);
        $logger = $this->prophesize(LoggerInterface::class);

        $marsRoverProjector = new MarsRoverProjector(
            $marsRoverRepository->reveal(),
            $logger->reveal()
        );

        $id = Uuid::fromString("00000000-0000-0000-0000-000000000000");
        $primitiveCommand = PrimitiveCommand::fromString('R');

        $primitiveCommandSentEvent = new PrimitiveCommandSent(
            $id,
            $primitiveCommand
        );

        $marsRoverProjector->applyPrimitiveCommandSent($primitiveCommandSentEvent);

        $logger
            ->critical(Argument::type("string"))
            ->shouldHaveBeenCalledTimes(0);

        $marsRoverRepository
            ->store(Argument::type(MarsRover::class))
            ->shouldHaveBeenCalledTimes(0);
    }

    public function test_it_should_handle_mars_rover_primitive_command_sent_event_when_mars_rover_not_exists(): void
    {
        $marsRoverRepository = $this->prophesize(MarsRoverRepository::class);
        $logger = $this->prophesize(LoggerInterface::class);

        $marsRoverProjector = new MarsRoverProjector(
            $marsRoverRepository->reveal(),
            $logger->reveal()
        );

        $id = Uuid::fromString("00000000-0000-0000-0000-000000000000");
        $primitiveCommand = PrimitiveCommand::fromString('F');

        $primitiveCommandSentEvent = new PrimitiveCommandSent(
            $id,
            $primitiveCommand
        );

        $marsRoverProjector->applyPrimitiveCommandSent($primitiveCommandSentEvent);

        $logger
            ->critical("Mars Rover with id: 00000000-0000-0000-0000-000000000000 not found!!!")
            ->shouldHaveBeenCalledTimes(1);

        $marsRoverRepository
            ->store(Argument::type(MarsRover::class))
            ->shouldHaveBeenCalledTimes(0);
    }

    public function test_it_should_handle_mars_rover_primitive_command_sent_event(): void
    {
        $id = Uuid::uuid4();
        $name = 'test-rover';
        $createdAt = new \DateTime();

        $marsRover = new MarsRover(
            $id->toString(),
            $name,
            $createdAt
        );

        $marsRoverRepository = $this->prophesize(MarsRoverRepository::class);
        $marsRoverRepository
            ->get($id->toString())
            ->willReturn($marsRover);

        $loggerInterface = $this->prophesize(LoggerInterface::class);

        $marsRoverProjector = new MarsRoverProjector(
            $marsRoverRepository->reveal(),
            $loggerInterface->reveal()
        );

        $primitiveCommand = PrimitiveCommand::fromString('F');

        $primitiveCommandSentEvent = new PrimitiveCommandSent(
            $id,
            $primitiveCommand
        );

        $marsRoverProjector->applyPrimitiveCommandSent($primitiveCommandSentEvent);

        $marsRoverRepository
            ->store($marsRover->withUpdateKm(1))
            ->shouldHaveBeenCalledTimes(1);
    }

    public function test_it_should_handle_mars_rover_complex_command_sent_event(): void
    {
        $id = Uuid::uuid4();
        $name = 'test-rover';
        $createdAt = new \DateTime();

        $marsRover = new MarsRover(
            $id->toString(),
            $name,
            $createdAt
        );

        $marsRoverRepository = $this->prophesize(MarsRoverRepository::class);
        $marsRoverRepository
            ->get($id->toString())
            ->willReturn($marsRover);

        $loggerInterface = $this->prophesize(LoggerInterface::class);

        $marsRoverProjector = new MarsRoverProjector(
            $marsRoverRepository->reveal(),
            $loggerInterface->reveal()
        );

        $complexCommand = ComplexCommand::fromString('FFRF');

        $complexCommandSentEvent = new ComplexCommandSent(
            $id,
            $complexCommand
        );

        $marsRoverProjector->applyComplexCommandSent($complexCommandSentEvent);

        $marsRoverRepository
            ->store($marsRover->withUpdateKm(3))
            ->shouldHaveBeenCalledTimes(1);
    }

}