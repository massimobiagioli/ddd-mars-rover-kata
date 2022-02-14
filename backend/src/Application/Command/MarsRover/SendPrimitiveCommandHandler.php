<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Command\MarsRover;

use MarsRoverKata\Domain\MarsRover\Exception\MarsRoverNotFoundException;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use Psr\Log\LoggerInterface;

class SendPrimitiveCommandHandler
{
    public function __construct(
        private MarsRoverRepository $marsRoverRepository,
        private LoggerInterface     $logger
    )
    {
    }

    public function __invoke(SendPrimitiveCommand $sendPrimitiveCommand)
    {
        $marsRover = $this->marsRoverRepository->get($sendPrimitiveCommand->getId());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$sendPrimitiveCommand->getId()->toString()} not found!!!");
            return;
        }
        $marsRover->sendCommand(
            $sendPrimitiveCommand->getPrimitiveCommand()
        );
        $this->marsRoverRepository->store($marsRover);
    }
}