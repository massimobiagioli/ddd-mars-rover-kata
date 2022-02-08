<?php
declare(strict_types=1);

namespace MarsRoverKata\UI\CLI\Command\MarsRover;

use MarsRoverKata\Application\Command\CommandBus;
use MarsRoverKata\Application\Command\MarsRover\PlaceMarsRover;
use MarsRoverKata\Domain\MarsRover\Coordinates;
use MarsRoverKata\Domain\MarsRover\Orientation;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PlaceMarsRoverCommand extends Command
{
    protected static $defaultName = 'app:mars-rover:place';
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
            ->addArgument('y', InputArgument::REQUIRED, 'Coordinate Y')
            ->addArgument('orientation', InputArgument::REQUIRED, 'Orientation');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = Uuid::fromString($input->getArgument('id'));
        $coordinates = Coordinates::create(
            $input->getArgument('x'),
            $input->getArgument('y')
        );
        $orientation = Orientation::fromString($input->getArgument('orientation'));

        $placeMarsRover = new PlaceMarsRover(
            $id,
            $coordinates,
            $orientation
        );

        $this->commandBus->dispatch($placeMarsRover);

        $output->writeln("Mars Rover with id: $id was placed on coordinates: {$input->getArgument('x')}, {$input->getArgument('y')} with orientation: {$input->getArgument('orientation')}");

        return self::SUCCESS;
    }
}