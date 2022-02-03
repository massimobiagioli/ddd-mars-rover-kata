<?php
declare(strict_types=1);

namespace MarsRoverKata\Infrastructure\Doctrine\ReadModel;

use Doctrine\ORM\EntityManagerInterface;
use MarsRoverKata\Application\Query\MarsRover\MarsRover;
use MarsRoverKata\Application\Query\MarsRover\MarsRoverRepository;
use Webmozart\Assert\Assert;

class MarsRoverRepositoryDoctrineImpl implements MarsRoverRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function get(string $id): MarsRover
    {
        $marsRover = $this->entityManager->find(MarsRover::class, $id);

        Assert::isInstanceOf($marsRover, MarsRover::class);

        return $marsRover;
    }

    public function store(MarsRover $marsRover): void
    {
        $this->entityManager->persist($marsRover);
        $this->entityManager->flush();
    }

    public function getAll(): array
    {
        $marsRoverFqcn = MarsRover::class;

        $query = $this->entityManager->createQuery(<<<DQL
            SELECT mars_rover
            FROM {$marsRoverFqcn} as mars_rover
            ORDER BY mars_rover.createdAt DESC
        DQL
        );

        return $query->getArrayResult();
    }
}