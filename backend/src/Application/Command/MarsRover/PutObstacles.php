<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Command\MarsRover;

use MarsRoverKata\Domain\MarsRover\Obstacles;
use Ramsey\Uuid\UuidInterface;

class PutObstacles
{
    public function __construct(
        private UuidInterface $id,
        private Obstacles $obstacles
    )
    {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getObstacles(): Obstacles
    {
        return $this->obstacles;
    }
}