<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Unit\Application\Command\MarsRover;

use Broadway\Domain\DomainMessage;
use H2P\Domain\AuctionEngine\Auction;
use MarsRoverKata\Application\Command\MarsRover\CreateMarsRover;
use MarsRoverKata\Application\Command\MarsRover\CreateMarsRoverHandler;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverCreated;
use MarsRoverKata\Domain\MarsRover\MarsRover;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use MarsRoverKata\Domain\MarsRover\Terrain;
use MarsRoverKata\Tests\Fixtures\MarsRover\MarsRoverBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Ramsey\Uuid\Uuid;

class CreateMarsRoverHandlerTest extends TestCase
{
    public function test_it_should_throw_exception_if_empty_name_provided(): void
    {
        $marsRoverRepository = $this->prophesize(MarsRoverRepository::class);

        $createMarsRoverHandler = new CreateMarsRoverHandler($marsRoverRepository->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Name cannot be empty');

        $createMarsRoverHandler(
            new CreateMarsRover(
                Uuid::uuid4(),
                '',
                Terrain::default(),
                new \DateTimeImmutable()
            )
        );
    }

    public function test_it_should_create_new_mars_rover(): void
    {
        $id = Uuid::uuid4();
        $name = 'test-rover';
        $terrain = Terrain::default();
        $createdAt = new \DateTimeImmutable();

        $createMarsRoverCommand = new CreateMarsRover(
            $id,
            $name,
            $terrain,
            $createdAt
        );

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

        $createMarsRoverCommandHandler = new CreateMarsRoverHandler($marsRoverRepository->reveal());

        $createMarsRoverCommandHandler($createMarsRoverCommand);

        $domainEventStream = $marsRover->getUncommittedEvents();

        $expected = [
            new MarsRoverCreated(
                $id,
                $name,
                $terrain,
                $createdAt
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