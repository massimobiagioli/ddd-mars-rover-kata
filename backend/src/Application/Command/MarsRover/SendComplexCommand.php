<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Command\MarsRover;

use MarsRoverKata\Domain\MarsRover\ComplexCommand;
use Ramsey\Uuid\UuidInterface;

class SendComplexCommand
{
    public function __construct(
        private UuidInterface  $id,
        private ComplexCommand $complexCommand,
    )
    {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getComplexCommand(): ComplexCommand
    {
        return $this->complexCommand;
    }
}