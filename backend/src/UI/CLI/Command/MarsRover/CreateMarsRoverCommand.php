<?php
declare(strict_types=1);

namespace MarsRoverKata\UI\CLI\Command\MarsRover;

use MarsRoverKata\Application\Command\CommandBus;
use MarsRoverKata\Application\Command\MarsRover\CreateMarsRover;
use MarsRoverKata\Domain\MarsRover\Obstacles;
use MarsRoverKata\Domain\MarsRover\Terrain;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMarsRoverCommand extends Command
{
    protected static $defaultName = 'app:mars-rover:create';
    private CommandBus $commandBus;

    public function __construct(
        CommandBus $commandBus,
        string     $name = null
    )
    {
        parent::__construct($name);
        $this->commandBus = $commandBus;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Mars Rover Name')
            ->addOption('terrain_width', 'tw', InputOption::VALUE_OPTIONAL, 'Terrain Width')
            ->addOption('terrain_height', 'th', InputOption::VALUE_OPTIONAL, 'Terrain Height')
            ->addOption('obstacle_x', 'ox', InputOption::VALUE_OPTIONAL, 'Obstacle x')
            ->addOption('obstacle_y', 'oy', InputOption::VALUE_OPTIONAL, 'Obstacle y');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = Uuid::uuid4();
        $name = $input->getArgument('name');
        $terrainWidth = $input->getOption('terrain_width');
        $terrainHeight = $input->getOption('terrain_height');
        $obstacleX = $input->getOption('obstacle_x');
        $obstacleY = $input->getOption('obstacle_y');

        $terrain = Terrain::default();
        if ($terrainWidth !== null && $terrainHeight !== null) {
            $terrain = Terrain::create($terrainWidth, $terrainHeight);
        }
        if ($obstacleX !== null && $obstacleY !== null) {
            $terrain = $terrain->withObstacles(Obstacles::fromArray([
                ['x' => $obstacleX, 'y' => $obstacleY]
            ]));
        }

        $createMarsRover = new CreateMarsRover(
            $id,
            $name,
            $terrain,
            new \DateTimeImmutable()
        );

        $this->commandBus->dispatch($createMarsRover);

        $output->writeln("Mars Rover with id: $id was created");

        return self::SUCCESS;
    }
}