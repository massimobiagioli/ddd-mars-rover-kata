<?php
declare(strict_types=1);

namespace MarsRoverKata\Infrastructure\Broadway\MarsRover;

use Broadway\EventHandling\EventBus;
use Broadway\EventSourcing\AggregateFactory\PublicConstructorAggregateFactory;
use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventStore\EventStore;
use Broadway\Repository\AggregateNotFoundException;
use H2P\Domain\AuctionEngine\Auction;
use MarsRoverKata\Domain\MarsRover\MarsRover;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use Ramsey\Uuid\UuidInterface;
use Webmozart\Assert\Assert;

class MarsRoverRepositoryImpl extends EventSourcingRepository implements MarsRoverRepository
{
    public function __construct(
        EventStore $eventStore,
        EventBus $eventBus,
        array $eventStreamDecorators = []
    ) {
        parent::__construct(
            $eventStore,
            $eventBus,
            MarsRover::class,
            new PublicConstructorAggregateFactory(),
            $eventStreamDecorators
        );
    }

    public function get(UuidInterface $id): ?MarsRover
    {
        try {
            $marsRover = $this->load($id);
            Assert::isInstanceOf($marsRover, MarsRover::class);
            return $marsRover;
        } catch (AggregateNotFoundException $e) {
            return null;
        }
    }

    public function store(MarsRover $marsRover): void
    {
        $this->save($marsRover);
    }
}