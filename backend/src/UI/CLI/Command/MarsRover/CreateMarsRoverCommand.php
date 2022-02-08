<?php
declare(strict_types=1);

namespace MarsRoverKata\UI\CLI\Command\MarsRover;

use MarsRoverKata\Application\Command\CommandBus;
use MarsRoverKata\Application\Command\MarsRover\CreateMarsRover;
use MarsRoverKata\Domain\MarsRover\Terrain;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
            ->addArgument('name', InputArgument::REQUIRED, 'Mars Rover Name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = Uuid::uuid4();
        $name = $input->getArgument('name');

        $createMarsRover = new CreateMarsRover(
            $id,
            $name,
            Terrain::default(),
            new \DateTimeImmutable()
        );

        $this->commandBus->dispatch($createMarsRover);

        $output->writeln("Mars Rover with id: $id was created");

        return self::SUCCESS;
    }
}