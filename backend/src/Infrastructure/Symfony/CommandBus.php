<?php
declare(strict_types=1);

namespace MarsRoverKata\Infrastructure\Symfony;

use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use MarsRoverKata\Application\Command\CommandBus as CommandBusInterface;

class CommandBus implements CommandBusInterface
{
    use HandleTrait;

    public function __construct(private MessageBusInterface $commandBus)
    {
        $this->messageBus = $this->commandBus;
    }

    /** @throws \Exception */
    public function dispatch(object $command): void
    {
        try {
            $this->handle($command);
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious() ?? $exception;
        }

    }
}
