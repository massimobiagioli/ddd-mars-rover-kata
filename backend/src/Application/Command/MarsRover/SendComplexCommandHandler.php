<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Command\MarsRover;

use MarsRoverKata\Domain\MarsRover\Exception\MarsRoverNotFoundException;
use MarsRoverKata\Domain\MarsRover\MarsRover;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use MarsRoverKata\Domain\MarsRover\Route\RouteService;
use Psr\Log\LoggerInterface;

class SendComplexCommandHandler
{
    public function __construct(
        private MarsRoverRepository $marsRoverRepository,
        private RouteService        $routeService,
        private LoggerInterface     $logger
    )
    {
    }

    public function __invoke(SendComplexCommand $sendComplexCommand)
    {
        /** @var MarsRover|null $marsRover */
        $marsRover = $this->marsRoverRepository->get($sendComplexCommand->getId());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$sendComplexCommand->getId()->toString()} not found!!!");
            return;
        }
        if ($marsRover->isPaused()) {
            return;
        }

        $coordinates = $marsRover->coordinates();
        $orientation = $marsRover->orientation();

        if ($coordinates === null || $orientation === null) {
            $this->logger->warning("Mars Rover with id: {$sendComplexCommand->getId()->toString()} is not placed yet!!!");
            return;
        }

        $route = $this->routeService->calculateRoute(
            $marsRover->terrain(),
            $coordinates,
            $orientation,
            $sendComplexCommand->getComplexCommand()
        );

        if ($route->hasObstacle()) {
            $marsRover->detectObstacle($route);
            $this->logger->warning("Mars Rover with id: {$sendComplexCommand->getId()->toString()} has detected an obstacle!!!");
            $this->marsRoverRepository->store($marsRover);
            return;
        }

        $marsRover->sendComplexCommand(
            $sendComplexCommand->getComplexCommand()
        );

        $this->marsRoverRepository->store($marsRover);
    }
}