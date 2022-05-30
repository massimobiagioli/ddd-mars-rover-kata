<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Command\MarsRover;

use MarsRoverKata\Domain\MarsRover\ComplexCommand;
use MarsRoverKata\Domain\MarsRover\Exception\MarsRoverNotFoundException;
use MarsRoverKata\Domain\MarsRover\MarsRover;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use MarsRoverKata\Domain\MarsRover\Route\RouteService;
use Psr\Log\LoggerInterface;

class SendPrimitiveCommandHandler
{
    public function __construct(
        private MarsRoverRepository $marsRoverRepository,
        private RouteService $routeService,
        private LoggerInterface     $logger
    )
    {
    }

    public function __invoke(SendPrimitiveCommand $sendPrimitiveCommand)
    {
        /** @var MarsRover|null $marsRover */
        $marsRover = $this->marsRoverRepository->get($sendPrimitiveCommand->getId());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$sendPrimitiveCommand->getId()->toString()} not found!!!");
            return;
        }

        if ($marsRover->isPaused()) {
            return;
        }

        $coordinates = $marsRover->coordinates();
        $orientation = $marsRover->orientation();

        if ($coordinates === null || $orientation === null) {
            $this->logger->warning("Mars Rover with id: {$sendPrimitiveCommand->getId()->toString()} is not placed yet!!!");
            return;
        }

        $complexCommand = ComplexCommand::fromString($sendPrimitiveCommand->getPrimitiveCommand()->toString());

        $route = $this->routeService->calculateRoute(
            $marsRover->terrain(),
            $coordinates,
            $orientation,
            $complexCommand
        );

        if ($route->hasObstacle()) {
            $this->logger->warning("Mars Rover with id: {$sendPrimitiveCommand->getId()->toString()} has detected an obstacle!!!");
            $marsRover->detectObstacle();
            $this->marsRoverRepository->store($marsRover);
            return;
        }

        $marsRover->sendCommand(
            $sendPrimitiveCommand->getPrimitiveCommand()
        );

        $this->marsRoverRepository->store($marsRover);
    }
}