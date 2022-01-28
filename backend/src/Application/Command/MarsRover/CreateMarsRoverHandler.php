<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Command\MarsRover;

use MarsRoverKata\Domain\MarsRover\MarsRover;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;

class CreateMarsRoverHandler
{
    public function __construct(
        private MarsRoverRepository $marsRoverRepository
    )
    {
    }

    public function __invoke(CreateMarsRover $createMarsRover)
    {
        $marsRover = MarsRover::create(
            $createMarsRover->getId(),
            $createMarsRover->getName(),
            $createMarsRover->getTerrain(),
            $createMarsRover->getCreatedAt()
        );
        $this->marsRoverRepository->store($marsRover);
    }
}