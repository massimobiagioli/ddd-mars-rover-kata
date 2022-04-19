<?php
declare(strict_types=1);

namespace MarsRoverKata\UI\CLI\Command\MarsRover;

use MarsRoverKata\Application\Command\CommandBus;
use MarsRoverKata\Application\Command\MarsRover\RepairMarsRover;
use MarsRoverKata\Domain\MarsRover\RepairResult;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RepairMarsRoverCommand extends Command
{
    protected static $defaultName = 'app:mars-rover:repair';
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
            ->addArgument('result', InputArgument::REQUIRED, 'Result (ok/ko)')
            ->addOption('failure', null, InputOption::VALUE_OPTIONAL, 'Failure (if ko)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = Uuid::fromString($input->getArgument('id'));
        $result = strtolower($input->getArgument('result'));

        if ($result !== 'ok' && $result !== 'ko') {
            $output->writeln('Result must be "ok" or "ko"');
            return self::FAILURE;
        }

        if ($result === 'ok') {
            $command = new RepairMarsRover(
                $id,
                RepairResult::ok(),
                new \DateTimeImmutable()
            );
            $this->commandBus->dispatch($command);

            $output->writeln("I wanna repair Mars Rover with id: $id");

            return self::SUCCESS;
        }

        $failure = $input->getOption('failure');
        if (empty($failure)) {
            $output->writeln('Failure is required');
            return self::FAILURE;
        }

        $command = new RepairMarsRover(
            $id,
            RepairResult::ko($failure),
            new \DateTimeImmutable()
        );
        $this->commandBus->dispatch($command);

        $output->writeln("Mars Rover with id: $id is broken !!!");
        return self::SUCCESS;
    }
}