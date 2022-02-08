<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Query\MarsRover\Projector;

use Broadway\ReadModel\Projector;
use MarsRoverKata\Application\Query\MarsRover\MarsRover;
use MarsRoverKata\Application\Query\MarsRover\MarsRoverRepository;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverCreated;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverPlaced;
use Psr\Log\LoggerInterface;

class MarsRoverProjector extends Projector
{
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
}