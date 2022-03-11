<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Command\MarsRover;

use MarsRoverKata\Domain\MarsRover\Exception\MarsRoverNotFoundException;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use Psr\Log\LoggerInterface;

class SendComplexCommandHandler
{
    public function __construct(
        private MarsRoverRepository $marsRoverRepository,
        private LoggerInterface     $logger
    )
    {
    }

    public function __invoke(SendComplexCommand $sendComplexCommand)
    {
        $marsRover = $this->marsRoverRepository->get($sendComplexCommand->getId());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$sendComplexCommand->getId()->toString()} not found!!!");
            return;
        }
        $marsRover->sendComplexCommand(
            $sendComplexCommand->getComplexCommand()
        );
        $this->marsRoverRepository->store($marsRover);
    }
}