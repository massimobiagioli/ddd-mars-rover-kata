<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Command\MarsRover;

use Broadway\Domain\DomainMessage;
use H2P\Domain\Booking\BookingId;
use H2P\Infrastructure\Timify\BookingDataDTO;
use MarsRoverKata\Application\Command\MarsRover\PlaceMarsRover;
use MarsRoverKata\Application\Command\MarsRover\PlaceMarsRoverHandler;
use MarsRoverKata\Application\Command\MarsRover\SendPrimitiveCommand;
use MarsRoverKata\Application\Command\MarsRover\SendPrimitiveCommandHandler;
use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverPlaced;
use MarsRoverKata\Domain\MarsRover\Event\PrimitiveCommandSent;
use MarsRoverKata\Domain\MarsRover\MarsRover;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use MarsRoverKata\Domain\MarsRover\Orientation;
use MarsRoverKata\Domain\MarsRover\PrimitiveCommand;
use MarsRoverKata\Domain\MarsRover\Terrain;
use MarsRoverKata\Tests\Fixtures\MarsRover\MarsRoverBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class SendPrimitiveCommandHandlerTest extends TestCase
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

    public function test_it_should_send_primitive_command_to_mars_rover(): void
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

        $marsRover->place(
            Coordinates::create(1, 3),
            Orientation::fromString('W')
        );

        $marsRover->getUncommittedEvents();

        $logger = $this->prophesize(LoggerInterface::class);

        $primitiveCommand = PrimitiveCommand::fromString('F');
        $sendPrimitiveCommand = new SendPrimitiveCommand(
            $id,
            $primitiveCommand
        );

        $sendPrimitiveCommandHandler = new SendPrimitiveCommandHandler(
            $marsRoverRepository->reveal(),
            $logger->reveal()
        );

        $sendPrimitiveCommandHandler($sendPrimitiveCommand);

        $domainEventStream = $marsRover->getUncommittedEvents();

        $expected = [
            new PrimitiveCommandSent(
                $id,
                $primitiveCommand
            )
        ];

        $events = iterator_to_array($domainEventStream->getIterator());

        $events = array_map(function (DomainMessage $event): object {
            return $event->getPayload();
        }, array_values($events));

        $this->assertEquals($expected, $events);

        $marsRoverRepository->store(Argument::type(MarsRover::class))->shouldHaveBeenCalled();
    }
}