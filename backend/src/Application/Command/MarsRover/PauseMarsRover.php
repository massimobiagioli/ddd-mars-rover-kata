<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Command\MarsRover;

use Ramsey\Uuid\UuidInterface;

class PauseMarsRover
{
    public function __construct(
        private UuidInterface $id
    )
    {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }
}