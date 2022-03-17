<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Query\MarsRover;

use Doctrine\ORM\Mapping as ORM;
use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Orientation;

/** @ORM\Entity @ORM\Table(name="read_model_mars_rover") */
class MarsRover
{
    public function __construct(
        /** @ORM\Id() @ORM\Column() */
        public string    $id,
        /** @ORM\Column(type="string") */
        public string    $name,
        /** @ORM\Column(type="datetime") */
        public \DateTime $createdAt,
        /** @ORM\Column(type="integer") */
        public ?int      $coordinate_x = null,
        /** @ORM\Column(type="integer") */
        public ?int      $coordinate_y = null,
        /** @ORM\Column(type="string") */
        public ?string   $orientation = null,
        /** @ORM\Column(type="integer") */
        public ?int      $km = 0,
        /** @ORM\Column(type="string") */
        public ?string    $status = null
    )
    {
    }

    public function withCoordinates(Coordinates $coordinates): self
    {
        $this->coordinate_x = $coordinates->x();
        $this->coordinate_y = $coordinates->y();
        return $this;
    }

    public function withOrientation(Orientation $orientation): self
    {
        $this->orientation = $orientation->toString();
        return $this;
    }

    public function withUpdateKm(int $diffKm): self
    {
        $this->km += $diffKm;
        return $this;
    }
}