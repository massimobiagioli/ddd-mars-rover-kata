<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Query\MarsRover\Projector;

use Broadway\ReadModel\Projector;
use MarsRoverKata\Application\Query\MarsRover\MarsRover;
use MarsRoverKata\Application\Query\MarsRover\MarsRoverRepository;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverCreated;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverPlaced;
use MarsRoverKata\Domain\MarsRover\Event\PrimitiveCommandSent;
use Psr\Log\LoggerInterface;

class MarsRoverProjector extends Projector
{
    private const COMMANDS_THAT_UPDATES_KM = ['F', 'B'];

    public function __construct(
        private MarsRoverRepository $marsRoverRepository,
        private LoggerInterface     $logger
    )
    {
    }

    public function applyMarsRoverCreated(MarsRoverCreated $event): void
    {
        $marsRover = new MarsRover(
            $event->getId()->toString(),
            $event->getName(),
            \DateTime::createFromImmutable($event->getCreatedAt())
        );
        $this->marsRoverRepository->store($marsRover);
    }

    public function applyMarsRoverPlaced(MarsRoverPlaced $event): void
    {
        $marsRover = $this->marsRoverRepository->get($event->getId()->toString());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$event->getId()->toString()} not found!!!");
            return;
        }

        $this->marsRoverRepository->store(
            $marsRover
                ->withCoordinates($event->getCoordinates())
                ->withOrientation($event->getOrientation())
        );
    }

    public function applyPrimitiveCommandSent(PrimitiveCommandSent $event): void
    {
        if (!$event
            ->getPrimitiveCommand()
            ->in(self::COMMANDS_THAT_UPDATES_KM)) {

            return;
        }

        $marsRover = $this->marsRoverRepository->get($event->getId()->toString());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$event->getId()->toString()} not found!!!");
            return;
        }

        $this->marsRoverRepository->store(
            $marsRover->updateKm(1)
        );
    }
}