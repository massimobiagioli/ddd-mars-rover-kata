<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Query\MarsRover;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity @ORM\Table(name="read_model_mars_rover")  */
class MarsRover
{
    public function __construct(
        /** @ORM\Id() @ORM\Column() */
        public string $id,
        /** @ORM\Column(type="string") */
        public string $name,
        /** @ORM\Column(type="datetime") */
        public \DateTimeImmutable $createdAt
    )
    {
    }
}