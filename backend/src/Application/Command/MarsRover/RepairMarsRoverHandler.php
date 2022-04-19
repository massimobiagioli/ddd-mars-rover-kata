<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Command\MarsRover;

use MarsRoverKata\Domain\MarsRover\Exception\MarsRoverNotFoundException;
use MarsRoverKata\Domain\MarsRover\MarsRoverRepository;
use Psr\Log\LoggerInterface;

class RepairMarsRoverHandler
{
    public function __construct(
        private MarsRoverRepository $marsRoverRepository,
        private LoggerInterface $logger
    )
    {
    }

    public function __invoke(RepairMarsRover $repairMarsRover)
    {
        $marsRover = $this->marsRoverRepository->get($repairMarsRover->getId());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$repairMarsRover->getId()->toString()} not found!!!");
            return;
        }

        if (!$marsRover->hasMaintenanceLightOn()) {
            return;
        }

        if ($repairMarsRover->getResult()->isOk()) {
            $marsRover->repair();
        } else {
            $marsRover->setBrokenWithFailure($repairMarsRover->getResult()->failure() ?? '');
        }

        $this->marsRoverRepository->store($marsRover);
    }
}