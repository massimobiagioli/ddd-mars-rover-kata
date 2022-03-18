<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Command\MarsRover;

use MarsRoverKata\Domain\MarsRover\Exception\MarsRoverNotFoundException;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use Psr\Log\LoggerInterface;

class PauseMarsRoverHandler
{
    public function __construct(
        private MarsRoverRepository $marsRoverRepository,
        private LoggerInterface $logger
    )
    {
    }

    public function __invoke(PauseMarsRover $pauseMarsRover)
    {
        $marsRover = $this->marsRoverRepository->get($pauseMarsRover->getId());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$pauseMarsRover->getId()->toString()} not found!!!");
            return;
        }
        $marsRover->pause();
        $this->marsRoverRepository->store($marsRover);
    }
}