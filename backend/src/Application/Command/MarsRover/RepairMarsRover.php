<?php
declare(strict_types=1);

namespace MarsRoverKata\Application\Command\MarsRover;

use MarsRoverKata\Domain\MarsRover\RepairResult;
use MarsRoverKata\Domain\MarsRover\Terrain;
use Ramsey\Uuid\UuidInterface;
use Webmozart\Assert\Assert;

class RepairMarsRover
{
    public function __construct(
        private UuidInterface $id,
        private RepairResult $repairResult,
        private \DateTimeImmutable $createdAt,
    )
    {
        Assert::notEmpty($id, 'Id cannot be empty');
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getResult(): RepairResult
    {
        return $this->repairResult;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}