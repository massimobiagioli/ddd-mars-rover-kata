<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Command\MarsRover;

use Broadway\Domain\DomainMessage;
use H2P\Domain\Booking\BookingId;
use H2P\Infrastructure\Timify\BookingDataDTO;
use MarsRoverKata\Application\Command\MarsRover\PutObstacles;
use MarsRoverKata\Application\Command\MarsRover\PutObstaclesHandler;
use MarsRoverKata\Domain\MarsRover\Event\TerrainUpdatedWithObstacles;
use MarsRoverKata\Domain\MarsRover\MarsRover;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use MarsRoverKata\Domain\MarsRover\Obstacles;
use MarsRoverKata\Domain\MarsRover\Terrain;
use MarsRoverKata\Tests\Fixtures\MarsRover\MarsRoverBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class PutObstaclesHandlerTest extends TestCase
{
    public function test_it_should_throw_exception_if_mars_rover_does_not_exists(): void
    {
        $id = Uuid::fromString('00000000-0000-0000-0000-000000000000');

        $marsRoverRepository = $this->prophesize(MarsRoverRepository::class);
        $marsRoverRepository
            ->get($id)
            ->willReturn(null);

        $logger = $this->prophesize(LoggerInterface::class);

        $putObstaclesHandler = new PutObstaclesHandler(
            $marsRoverRepository->reveal(),
            $logger->reveal()
        );

        $obstaclesData = [
            ['x' => 1, 'y' => 2],
        ];
        $obstacles = Obstacles::fromArray($obstaclesData);

        $putObstaclesHandler(
            new PutObstacles(
                $id,
                $obstacles
            )
        );

        $logger
            ->critical("Mars Rover with id: 00000000-0000-0000-0000-000000000000 not found!!!")
            ->shouldHaveBeenCalledTimes(1);
    }

    public function test_it_should_put_new_obstacle_on_terrain(): void
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

        $obstaclesData = [
            ['x' => 1, 'y' => 2],
        ];
        $obstacles = Obstacles::fromArray($obstaclesData);
        $putObstaclesCommand = new PutObstacles(
            $id,
            $obstacles
        );

        $putObstaclesHandler = new PutObstaclesHandler(
            $marsRoverRepository->reveal(),
            $logger->reveal()
        );

        $putObstaclesHandler($putObstaclesCommand);

        $domainEventStream = $marsRover->getUncommittedEvents();

        $expected = [
            new TerrainUpdatedWithObstacles(
                $id,
                $obstacles
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