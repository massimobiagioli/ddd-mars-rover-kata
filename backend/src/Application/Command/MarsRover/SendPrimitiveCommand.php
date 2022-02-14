<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Command\MarsRover;

use MarsRoverKata\Domain\MarsRover\PrimitiveCommand;
use Ramsey\Uuid\UuidInterface;

class SendPrimitiveCommand
{
    public function __construct(
        private UuidInterface $id,
        private PrimitiveCommand $primitiveCommand,
    )
    {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getPrimitiveCommand(): PrimitiveCommand
    {
        return $this->primitiveCommand;
    }
}