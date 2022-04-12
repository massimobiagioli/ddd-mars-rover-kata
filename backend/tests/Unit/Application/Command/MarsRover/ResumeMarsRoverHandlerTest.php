<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Command\MarsRover;

use Broadway\Domain\DomainMessage;
use H2P\Domain\Booking\BookingId;
use H2P\Infrastructure\Timify\BookingDataDTO;
use MarsRoverKata\Application\Command\MarsRover\PauseMarsRover;
use MarsRoverKata\Application\Command\MarsRover\PauseMarsRoverHandler;
use MarsRoverKata\Application\Command\MarsRover\ResumeMarsRover;
use MarsRoverKata\Application\Command\MarsRover\ResumeMarsRoverHandler;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverPaused;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverResumed;
use MarsRoverKata\Domain\MarsRover\MarsRover;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use MarsRoverKata\Domain\MarsRover\Terrain;
use MarsRoverKata\Tests\Fixtures\MarsRover\MarsRoverBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class ResumeMarsRoverHandlerTest extends TestCase
{
    public function test_it_should_throw_exception_if_mars_rover_does_not_exists(): void
    {
        $id = Uuid::fromString('00000000-0000-0000-0000-000000000000');

        $marsRoverRepository = $this->prophesize(MarsRoverRepository::class);
        $marsRoverRepository
            ->get($id)
            ->willReturn(null);

        $logger = $this->prophesize(LoggerInterface::class);

        $resumeMarsRoverHandler = new ResumeMarsRoverHandler(
            $marsRoverRepository->reveal(),
            $logger->reveal()
        );

        $resumeMarsRoverHandler(
            new ResumeMarsRover(
                $id
            )
        );

        $logger
            ->critical("Mars Rover with id: 00000000-0000-0000-0000-000000000000 not found!!!")
            ->shouldHaveBeenCalledTimes(1);
    }

    public function test_it_should_resume_a_paused_mars_rover(): void
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

        $logger = $this->prophesize(LoggerInterface::class);

        $pauseMarsRoverCommand = new PauseMarsRover($id);
        $pauseMarsRoverCommandHandler = new PauseMarsRoverHandler(
            $marsRoverRepository->reveal(),
            $logger->reveal()
        );
        $pauseMarsRoverCommandHandler($pauseMarsRoverCommand);

        $marsRover->getUncommittedEvents();

        $resumeMarsRoverCommand = new ResumeMarsRover($id);
        $resumeMarsRoverCommandHandler = new ResumeMarsRoverHandler(
            $marsRoverRepository->reveal(),
            $logger->reveal()
        );
        $resumeMarsRoverCommandHandler($resumeMarsRoverCommand);

        $domainEventStream = $marsRover->getUncommittedEvents();

        $expected = [
            new MarsRoverResumed($id)
        ];

        $events = iterator_to_array($domainEventStream->getIterator());

        $events = array_map(function (DomainMessage $event): object {
            return $event->getPayload();
        }, array_values($events));

        $this->assertEquals($expected, $events);

        $marsRoverRepository->store(Argument::type(MarsRover::class))->shouldHaveBeenCalled();
    }
}