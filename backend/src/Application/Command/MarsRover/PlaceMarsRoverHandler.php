<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Command\MarsRover;

use MarsRoverKata\Domain\MarsRover\Exception\MarsRoverNotFoundException;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use Psr\Log\LoggerInterface;

class PlaceMarsRoverHandler
{
    public function __construct(
        private MarsRoverRepository $marsRoverRepository,
        private LoggerInterface $logger
    )
    {
    }

    public function __invoke(PlaceMarsRover $placeMarsRover)
    {
        $marsRover = $this->marsRoverRepository->get($placeMarsRover->getId());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$placeMarsRover->getId()->toString()} not found!!!");
            return;
        }
        $marsRover->place(
            $placeMarsRover->getCoordinates(),
            $placeMarsRover->getOrientation()
        );
        $this->marsRoverRepository->store($marsRover);
    }
}