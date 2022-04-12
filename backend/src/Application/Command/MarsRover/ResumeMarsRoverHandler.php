<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Command\MarsRover;

use MarsRoverKata\Domain\MarsRover\Exception\MarsRoverNotFoundException;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use Psr\Log\LoggerInterface;

class ResumeMarsRoverHandler
{
    public function __construct(
        private MarsRoverRepository $marsRoverRepository,
        private LoggerInterface $logger
    )
    {
    }

    public function __invoke(ResumeMarsRover $resumeMarsRover)
    {
        $marsRover = $this->marsRoverRepository->get($resumeMarsRover->getId());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$resumeMarsRover->getId()->toString()} not found!!!");
            return;
        }

        if (!$marsRover->isPaused()) {
            return;
        }

        $marsRover->resume();
        $this->marsRoverRepository->store($marsRover);
    }
}