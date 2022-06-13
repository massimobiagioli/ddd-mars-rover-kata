<?php
declare(strict_types=1);

namespace MarsRoverKata\Infrastructure\Broadway\MarsRover\Processor;

use Broadway\Processor\Processor;
use MarsRoverKata\Domain\MarsRover\Event\ObstacleDetected;
use MarsRoverKata\Domain\MarsRover\MarsRover;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use MarsRoverKata\Domain\MarsRover\Route\Route;
use MarsRoverKata\Domain\MarsRover\Route\RouteService;
use Psr\Log\LoggerInterface;

class ObstacleDetectedProcessor extends Processor
{
    public function __construct(
        private MarsRoverRepository $marsRoverRepository,
        private LoggerInterface     $logger
    )
    {
    }

    public function handleObstacleDetected(ObstacleDetected $event): void
    {
        /** @var MarsRover|null $marsRover */
        $marsRover = $this->marsRoverRepository->get($event->getId());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$event->getId()->toString()} not found!!!");
            return;
        }

        $altRoutesData = $event->getRoute()->altRoutes();
        $autoSelectedRoute = Route::fromArray($altRoutesData[0]);

        $marsRover->sendComplexCommand($autoSelectedRoute->complexCommand());

        $this->marsRoverRepository->store($marsRover);
    }
}