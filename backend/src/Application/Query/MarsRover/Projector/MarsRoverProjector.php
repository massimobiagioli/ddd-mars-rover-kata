<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Query\MarsRover\Projector;

use Broadway\ReadModel\Projector;
use MarsRoverKata\Application\Query\MarsRover\MarsRover;
use MarsRoverKata\Application\Query\MarsRover\MarsRoverRepository;
use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Event\ComplexCommandSent;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverCreated;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverPaused;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverPlaced;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverRepaired;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverResumed;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverSetBrokenWithFailure;
use MarsRoverKata\Domain\MarsRover\Event\PrimitiveCommandSent;
use MarsRoverKata\Domain\MarsRover\Orientation;
use MarsRoverKata\Domain\MarsRover\Status;
use Psr\Log\LoggerInterface;

class MarsRoverProjector extends Projector
{
    private const COMMAND_STATUS_MAP = [
        'F' => [
            'N' => [
                'offsetX' => 0,
                'offsetY' => 1,
                'newOrientation' => 'N'
            ],
            'S' => [
                'offsetX' => 0,
                'offsetY' => -1,
                'newOrientation' => 'S'
            ],
            'E' => [
                'offsetX' => 1,
                'offsetY' => 0,
                'newOrientation' => 'E'
            ],
            'W' => [
                'offsetX' => -1,
                'offsetY' => 0,
                'newOrientation' => 'W'
            ]
        ],
        'B' => [
            'N' => [
                'offsetX' => 0,
                'offsetY' => -1,
                'newOrientation' => 'N'
            ],
            'S' => [
                'offsetX' => 0,
                'offsetY' => 1,
                'newOrientation' => 'S'
            ],
            'E' => [
                'offsetX' => -1,
                'offsetY' => 0,
                'newOrientation' => 'E'
            ],
            'W' => [
                'offsetX' => 1,
                'offsetY' => 0,
                'newOrientation' => 'W'
            ]
        ],
        'L' => [
            'N' => [
                'offsetX' => 0,
                'offsetY' => 0,
                'newOrientation' => 'W'
            ],
            'S' => [
                'offsetX' => 0,
                'offsetY' => 0,
                'newOrientation' => 'E'
            ],
            'E' => [
                'offsetX' => 0,
                'offsetY' => 0,
                'newOrientation' => 'N'
            ],
            'W' => [
                'offsetX' => 0,
                'offsetY' => 0,
                'newOrientation' => 'S'
            ]
        ],
        'R' => [
            'N' => [
                'offsetX' => 0,
                'offsetY' => 0,
                'newOrientation' => 'E'
            ],
            'S' => [
                'offsetX' => 0,
                'offsetY' => 0,
                'newOrientation' => 'W'
            ],
            'E' => [
                'offsetX' => 0,
                'offsetY' => 0,
                'newOrientation' => 'S'
            ],
            'W' => [
                'offsetX' => 0,
                'offsetY' => 0,
                'newOrientation' => 'N'
            ]
        ]
    ];

    public function __construct(
        private MarsRoverRepository $marsRoverRepository,
        private LoggerInterface     $logger
    )
    {
    }

    public function applyMarsRoverCreated(MarsRoverCreated $event): void
    {
        $marsRover = new MarsRover(
            $event->getId()->toString(),
            $event->getName(),
            \DateTime::createFromImmutable($event->getCreatedAt())
        );
        $this->marsRoverRepository->store($marsRover->withStatus(Status::created()));
    }

    public function applyMarsRoverPlaced(MarsRoverPlaced $event): void
    {
        $marsRover = $this->marsRoverRepository->get($event->getId()->toString());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$event->getId()->toString()} not found!!!");
            return;
        }

        $this->marsRoverRepository->store(
            $marsRover
                ->withCoordinates($event->getCoordinates())
                ->withOrientation($event->getOrientation())
                ->withStatus(Status::placed())
        );
    }

    public function applyPrimitiveCommandSent(PrimitiveCommandSent $event): void
    {
        $marsRover = $this->marsRoverRepository->get($event->getId()->toString());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$event->getId()->toString()} not found!!!");
            return;
        }

        if ($marsRover->orientation !== null) {
            $commandMapEntry = self::COMMAND_STATUS_MAP[$event->getPrimitiveCommand()->toString()][$marsRover->orientation];
            $newCoordinates = Coordinates::create(
                $marsRover->coordinate_x + $commandMapEntry['offsetX'],
                $marsRover->coordinate_y + $commandMapEntry['offsetY']
            );
            $marsRover = $marsRover
                ->withCoordinates($newCoordinates)
                ->withOrientation(Orientation::fromString($commandMapEntry['newOrientation']));
        }

        if ($event
            ->getPrimitiveCommand()
            ->canUpdateKm()) {

            $marsRover = $marsRover->withUpdateKm(1);
        }

        $this->marsRoverRepository->store(
            $marsRover
        );
    }

    public function applyComplexCommandSent(ComplexCommandSent $event): void
    {
        $marsRover = $this->marsRoverRepository->get($event->getId()->toString());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$event->getId()->toString()} not found!!!");
            return;
        }

        $offset = 0;
        $primitiveCommands = $event->getComplexCommand()->getPrimitiveCommands();
        foreach ($primitiveCommands as $primitiveCommand) {
            if ($marsRover->orientation !== null) {
                $commandMapEntry = self::COMMAND_STATUS_MAP[$primitiveCommand->toString()][$marsRover->orientation];
                $newCoordinates = Coordinates::create(
                    $marsRover->coordinate_x + $commandMapEntry['offsetX'],
                    $marsRover->coordinate_y + $commandMapEntry['offsetY']
                );
                $marsRover = $marsRover
                    ->withCoordinates($newCoordinates)
                    ->withOrientation(Orientation::fromString($commandMapEntry['newOrientation']));
            }

            if ($primitiveCommand->canUpdateKm()) {
                $offset++;
            }
        }

        $this->marsRoverRepository->store(
            $marsRover->withUpdateKm($offset)
        );
    }

    public function applyMarsRoverPaused(MarsRoverPaused $event): void
    {
        $marsRover = $this->marsRoverRepository->get($event->getId()->toString());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$event->getId()->toString()} not found!!!");
            return;
        }

        $this->marsRoverRepository->store(
            $marsRover->withStatus(Status::paused())
        );
    }

    public function applyMarsRoverResumed(MarsRoverResumed $event): void
    {
        $marsRover = $this->marsRoverRepository->get($event->getId()->toString());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$event->getId()->toString()} not found!!!");
            return;
        }

        $this->marsRoverRepository->store(
            $marsRover->withStatus(Status::placed())
        );
    }

    public function applyMarsRoverRepaired(MarsRoverRepaired $event): void
    {
        $marsRover = $this->marsRoverRepository->get($event->getId()->toString());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$event->getId()->toString()} not found!!!");
            return;
        }

        $this->marsRoverRepository->store(
            $marsRover->withResetMaintenanceStatus()
        );
    }

    public function applyMarsRoverSetBrokenWithFailure(MarsRoverSetBrokenWithFailure $event): void
    {
        $marsRover = $this->marsRoverRepository->get($event->getId()->toString());
        if ($marsRover === null) {
            $this->logger->critical("Mars Rover with id: {$event->getId()->toString()} not found!!!");
            return;
        }

        $this->marsRoverRepository->store(
            $marsRover
                ->withStatus(Status::broken())
                ->withFailure($event->getFailure())
        );
    }
}