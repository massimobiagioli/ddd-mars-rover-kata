<?php
declare(strict_types=1);

namespace MarsRoverKata\UI\CLI\Command\MarsRover;

use MarsRoverKata\Application\Command\CommandBus;
use MarsRoverKata\Application\Command\MarsRover\PutObstacles;
use MarsRoverKata\Domain\MarsRover\Obstacles;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PutObstacleCommand extends Command
{
    protected static $defaultName = 'app:mars-rover:put-obstacle';
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
            ->addArgument('id', InputArgument::REQUIRED, 'Mars Rover ID')
            ->addArgument('x', InputArgument::REQUIRED, 'Coordinate X')
            ->addArgument('y', InputArgument::REQUIRED, 'Coordinate Y');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = Uuid::fromString($input->getArgument('id'));

        $obstaclesData = [
            [
                'x' => (int)$input->getArgument('x'),
                'y' => (int)$input->getArgument('y')
            ]
        ];
        $obstacles = Obstacles::fromArray($obstaclesData);

        $putObstacle = new PutObstacles(
            $id,
            $obstacles
        );

        $this->commandBus->dispatch($putObstacle);

        $output->writeln("New obstacle was putted on coordinates: {$input->getArgument('x')}, {$input->getArgument('y')} for Mars Rover with id: $id");

        return self::SUCCESS;
    }
}