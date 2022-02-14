<?php
declare(strict_types=1);

namespace MarsRoverKata\UI\CLI\Command\MarsRover;

use MarsRoverKata\Application\Command\CommandBus;
use MarsRoverKata\Application\Command\MarsRover\SendPrimitiveCommand;
use MarsRoverKata\Domain\MarsRover\PrimitiveCommand;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendPrimitiveCommandToMarsRoverCommand extends Command
{
    protected static $defaultName = 'app:mars-rover:send-primitive-command';
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
            ->addArgument('primitive_command', InputArgument::REQUIRED, 'Primitive Command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = Uuid::fromString($input->getArgument('id'));
        $primitiveCommand = PrimitiveCommand::fromString($input->getArgument('primitive_command'));

        $sendPrimitiveCommand = new SendPrimitiveCommand(
            $id,
            $primitiveCommand
        );

        $this->commandBus->dispatch($sendPrimitiveCommand);

        $output->writeln("Command: {$input->getArgument('primitive_command')} was sent to Mars Rover with id: $id");

        return self::SUCCESS;
    }
}