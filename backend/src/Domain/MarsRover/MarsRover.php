<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use MarsRoverKata\Domain\MarsRover\Event\ComplexCommandSent;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverCreated;
use MarsRoverKata\Domain\MarsRover\Event\MarsRoverPlaced;
use MarsRoverKata\Domain\MarsRover\Event\PrimitiveCommandSent;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Webmozart\Assert\Assert;

class MarsRover extends EventSourcedAggregateRoot
{
    private UuidInterface $id;
    private string $name;
    private Terrain $terrain;
    private \DateTimeImmutable $createdAt;
    private ?Coordinates $coordinates;
    private ?Orientation $orientation;

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

    public function __construct()
    {
        $this->id = Uuid::fromString(Uuid::NIL);
        $this->name = '';
        $this->terrain = Terrain::default();
        $this->createdAt = new \DateTimeImmutable();
        $this->coordinates = null;
        $this->orientation = null;
    }

    public static function create(
        UuidInterface      $id,
        string             $name,
        Terrain            $terrain,
        \DateTimeImmutable $createdAt
    ): self
    {
        $marsRover = new self();
        $marsRover->apply(new MarsRoverCreated(
            $id,
            $name,
            $terrain,
            $createdAt
        ));
        return $marsRover;
    }

    public function place(
        Coordinates $coordinates,
        Orientation $orientation
    ): void
    {
        $this->coordinates = Coordinates::create(
            $this->terrain->capX($coordinates->x()),
            $this->terrain->capY($coordinates->y())
        );
        $this->orientation = $orientation;

        $this->apply(new MarsRoverPlaced(
            $this->id,
            $coordinates,
            $orientation
        ));
    }

    public function sendCommand(PrimitiveCommand $getPrimitiveCommand): void
    {
        $this->apply(new PrimitiveCommandSent(
            $this->id,
            $getPrimitiveCommand
        ));
    }

    public function sendComplexCommand(ComplexCommand $getComplexCommand): void
    {
        $this->apply(new ComplexCommandSent(
            $this->id,
            $getComplexCommand
        ));
    }

    public function getAggregateRootId(): string
    {
        return $this->id->toString();
    }

    protected function applyMarsRoverCreated(MarsRoverCreated $event): void
    {
        $this->id = $event->getId();
        $this->name = $event->getName();
        $this->terrain = $event->getTerrain();
        $this->createdAt = $event->getCreatedAt();
    }

    protected function applyMarsRoverPlaced(MarsRoverPlaced $event): void
    {
        $this->coordinates = $event->getCoordinates();
        $this->orientation = $event->getOrientation();
    }

    protected function applyPrimitiveCommandSent(PrimitiveCommandSent $event): void
    {
        $this->handlePrimitiveCommand($event->getPrimitiveCommand());
    }

    protected function applyComplexCommandSent(ComplexCommandSent $event): void
    {
        Assert::notNull($this->orientation, 'Mars Rover is not placed yet');
        Assert::notNull($this->coordinates, 'Mars Rover is not placed yet');

        $primitiveCommands = $event->getComplexCommand()->getPrimitiveCommands();
        foreach ($primitiveCommands as $primitiveCommand) {
            $this->handlePrimitiveCommand($primitiveCommand);
        }
    }

    private function handlePrimitiveCommand(PrimitiveCommand $primitiveCommand): void
    {
        Assert::notNull($this->orientation, 'Mars Rover is not placed yet');
        Assert::notNull($this->coordinates, 'Mars Rover is not placed yet');
        Assert::keyExists(self::COMMAND_STATUS_MAP, $primitiveCommand->toString(), 'Bad command');

        $commandMapEntry = self::COMMAND_STATUS_MAP[$primitiveCommand->toString()][$this->orientation->toString()];

        $this->coordinates = Coordinates::create(
            $this->terrain->capX($this->coordinates->x() + $commandMapEntry['offsetX']),
            $this->terrain->capY($this->coordinates->y() + $commandMapEntry['offsetY'])
        );

        $this->orientation = Orientation::fromString($commandMapEntry['newOrientation']);
    }
}