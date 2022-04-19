<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Query\MarsRover;

use Doctrine\ORM\Mapping as ORM;
use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Orientation;
use MarsRoverKata\Domain\MarsRover\Status;

/** @ORM\Entity @ORM\Table(name="read_model_mars_rover") */
class MarsRover
{
    private const KM_REPAIR_THRESHOLD = 10;

    public function __construct(
        /** @ORM\Id() @ORM\Column() */
        public string    $id,
        /** @ORM\Column(type="string") */
        public string    $name,
        /** @ORM\Column(type="datetime") */
        public \DateTime $createdAt,
        /** @ORM\Column(type="integer", nullable="true") */
        public ?int      $coordinate_x = null,
        /** @ORM\Column(type="integer", nullable="true") */
        public ?int      $coordinate_y = null,
        /** @ORM\Column(type="string", nullable="true") */
        public ?string   $orientation = null,
        /** @ORM\Column(type="integer", nullable="true") */
        public ?int      $km = 0,
        /** @ORM\Column(type="integer", nullable="true") */
        public ?int      $km_maintenance = 0,
        /** @ORM\Column(type="string", nullable="true") */
        public ?string   $status = null,
        /** @ORM\Column(type="integer", nullable="true") */
        public ?int      $maintenance_light = 0,
        /** @ORM\Column(type="string", nullable="true") */
        public ?string   $failure = '',
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

    public function withUpdateKm(int $offset): self
    {
        $this->km += $offset;
        $this->km_maintenance += $offset;

        if ($this->km_maintenance >= self::KM_REPAIR_THRESHOLD) {
            $this->maintenance_light = 1;
        }

        return $this;
    }

    public function withStatus(Status $status): self
    {
        $this->status = $status->toString();
        return $this;
    }

    public function withResetMaintenanceStatus(): self
    {
        $this->km_maintenance = 0;
        $this->maintenance_light = 0;
        $this->status = Status::placed()->toString();
        return $this;
    }

    public function withFailure(string $failure): self
    {
        $this->failure = $failure;
        return $this;
    }
}