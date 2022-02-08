<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Query\MarsRover;

interface MarsRoverRepository
{
    public function get(string $id): ?MarsRover;
    public function store(MarsRover $marsRover): void;
    public function getAll(): array;
}