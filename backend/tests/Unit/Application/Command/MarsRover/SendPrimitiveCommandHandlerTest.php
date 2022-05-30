<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Command\MarsRover;

use Broadway\Domain\DomainMessage;
use H2P\Domain\Booking\BookingId;
use H2P\Infrastructure\Timify\BookingDataDTO;
use MarsRoverKata\Application\Command\MarsRover\SendPrimitiveCommand;
use MarsRoverKata\Application\Command\MarsRover\SendPrimitiveCommandHandler;
use MarsRoverKata\Domain\MarsRover\ComplexCommand;
use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Event\ObstacleDetected;
use MarsRoverKata\Domain\MarsRover\Event\ObstacleDetectedLightTurnedOn;
use MarsRoverKata\Domain\MarsRover\Event\PrimitiveCommandSent;
use MarsRoverKata\Domain\MarsRover\MarsRover;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use MarsRoverKata\Domain\MarsRover\Obstacles;
use MarsRoverKata\Domain\MarsRover\Orientation;
use MarsRoverKata\Domain\MarsRover\PrimitiveCommand;
use MarsRoverKata\Domain\MarsRover\Route\Route;
use MarsRoverKata\Domain\MarsRover\Route\RouteService;
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

        $terrain = Terrain::default();
        $departure = Coordinates::create(0, 0);
        $orientation = Orientation::fromString('N');
        $primitiveCommand = PrimitiveCommand::fromString('F');
        $command = ComplexCommand::fromString($primitiveCommand->toString());

        $routeData = [
            ['coordinates' => ["x" => 0, "y" => 1], 'orientation' => 'N'],
        ];
        $route = Route::fromArray($routeData);

        $routeService = $this->prophesize(RouteService::class);
        $routeService
            ->calculateRoute(
                $terrain,
                $departure,
                $orientation,
                $command
            )
            ->willReturn($route);

        $sendPrimitiveCommandHandler = new SendPrimitiveCommandHandler(
            $marsRoverRepository->reveal(),
            $routeService->reveal(),
            $logger->reveal()
        );

        $sendPrimitiveCommandHandler(
            new SendPrimitiveCommand(
                $id,
                $primitiveCommand
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

        $terrain = Terrain::default();
        $departure = Coordinates::create(1, 3);
        $orientation = Orientation::fromString('W');
        $primitiveCommand = PrimitiveCommand::fromString('F');
        $command = ComplexCommand::fromString($primitiveCommand->toString());

        $routeData = [
            ['coordinates' => ["x" => 1, "y" => 2], 'orientation' => 'W'],
        ];
        $route = Route::fromArray($routeData);

        $routeService = $this->prophesize(RouteService::class);
        $routeService
            ->calculateRoute(
                $terrain,
                $departure,
                $orientation,
                $command
            )
            ->willReturn($route);

        $sendPrimitiveCommand = new SendPrimitiveCommand(
            $id,
            $primitiveCommand
        );

        $sendPrimitiveCommandHandler = new SendPrimitiveCommandHandler(
            $marsRoverRepository->reveal(),
            $routeService->reveal(),
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

        $marsRoverRepository = $this->prophesize(MarsRoverRepository::class);
        $marsRoverRepository
            ->get($id)
            ->willReturn($marsRover);

        $marsRover->place(
            Coordinates::create(1, 3),
            Orientation::fromString('W')
        );

        $marsRover->pause();

        $marsRover->getUncommittedEvents();

        $logger = $this->prophesize(LoggerInterface::class);

        $terrain = Terrain::default();
        $departure = Coordinates::create(1, 3);
        $orientation = Orientation::fromString('W');
        $primitiveCommand = PrimitiveCommand::fromString('F');
        $command = ComplexCommand::fromString($primitiveCommand->toString());

        $routeData = [
            ['coordinates' => ["x" => 1, "y" => 2], 'orientation' => 'W'],
        ];
        $route = Route::fromArray($routeData);

        $routeService = $this->prophesize(RouteService::class);
        $routeService
            ->calculateRoute(
                $terrain,
                $departure,
                $orientation,
                $command
            )
            ->willReturn($route);

        $sendPrimitiveCommand = new SendPrimitiveCommand(
            $id,
            $primitiveCommand
        );

        $sendPrimitiveCommandHandler = new SendPrimitiveCommandHandler(
            $marsRoverRepository->reveal(),
            $routeService->reveal(),
            $logger->reveal()
        );

        $sendPrimitiveCommandHandler($sendPrimitiveCommand);

        $domainEventStream = $marsRover->getUncommittedEvents();
        $events = iterator_to_array($domainEventStream->getIterator());

        $this->assertCount(0, $events);

        $marsRoverRepository->store(Argument::type(MarsRover::class))->shouldNotHaveBeenCalled();
    }

    public function test_it_should_turn_on_obstacle_detected_light(): void
    {
        $id = Uuid::uuid4();
        $name = 'test-rover';
        $terrain = Terrain::create(9, 9)->withObstacles(Obstacles::fromArray([
            ['x' => 0, 'y' => 1]
        ]));
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
            Coordinates::create(0, 0),
            Orientation::fromString('N')
        );

        $marsRover->getUncommittedEvents();

        $logger = $this->prophesize(LoggerInterface::class);

        $departure = Coordinates::create(0, 0);
        $orientation = Orientation::fromString('N');
        $primitiveCommand = PrimitiveCommand::fromString('F');
        $command = ComplexCommand::fromString($primitiveCommand->toString());

        $routeData = [
            ['coordinates' => ["x" => 0, "y" => 1], 'orientation' => 'N'],
        ];
        $route = Route::fromArray($routeData)->withObstacle();

        $routeService = $this->prophesize(RouteService::class);
        $routeService
            ->calculateRoute(
                $terrain,
                $departure,
                $orientation,
                $command
            )
            ->willReturn($route);

        $sendPrimitiveCommand = new SendPrimitiveCommand(
            $id,
            $primitiveCommand
        );

        $sendPrimitiveCommandHandler = new SendPrimitiveCommandHandler(
            $marsRoverRepository->reveal(),
            $routeService->reveal(),
            $logger->reveal()
        );

        $sendPrimitiveCommandHandler($sendPrimitiveCommand);

        $domainEventStream = $marsRover->getUncommittedEvents();

        $expected = [
            new ObstacleDetectedLightTurnedOn(
                $id
            ),
            new ObstacleDetected(
                $id,
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