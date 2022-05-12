<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Command\MarsRover;

use MarsRoverKata\Domain\MarsRover\Exception\MarsRoverNotFoundException;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use Psr\Log\LoggerInterface;

class PutObstaclesHandler
{
    public function __construct(
        private MarsRoverRepository $marsRoverRepository,
        private LoggerInterface $logger
    )
    {
    }

    public function __invoke(PutObstacles $putObstacles)
    {
        $marsRover = $this->marsRoverRepository->get($putObstacles->getId());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$putObstacles->getId()->toString()} not found!!!");
            return;
        }

        $marsRover->updateTerrainWithObstacles(
            $putObstacles->getObstacles()
        );

        $this->marsRoverRepository->store($marsRover);
    }
}