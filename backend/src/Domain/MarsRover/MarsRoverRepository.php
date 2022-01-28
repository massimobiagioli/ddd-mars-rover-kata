<?php

declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover;

use Ramsey\Uuid\UuidInterface;

interface MarsRoverRepository
{
    public function get(UuidInterface $id): MarsRover;

    public function store(MarsRover $marsRover): void;
}
