<?php

declare(strict_types=1);

namespace MarsRoverKata\Application\Command;

interface CommandBus
{
    public function dispatch(object $command): void;
}
