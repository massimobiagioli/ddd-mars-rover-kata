<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Query\MarsRover\Projector;

use Broadway\ReadModel\Projector;
use MarsRoverKata\Application\Query\MarsRover\MarsRover;
use MarsRoverKata\Application\Query\MarsRover\MarsRoverRepository;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverCreated;

class MarsRoverProjector extends Projector
{
    public function __construct(
        private MarsRoverRepository $marsRoverRepository,
    )
    {
    }

    public function applyMarsRoverCreated(MarsRoverCreated $event): void
    {
        $marsRover = new MarsRover(
            $event->getId()->toString(),
            $event->getName(),
            $event->getCreatedAt()
        );
        $this->marsRoverRepository->store($marsRover);
    }
}