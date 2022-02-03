<?php
declare(strict_types=1);

use MarsRoverKata\Application\Command\CommandBus;
use MarsRoverKata\Kernel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

$classToFileName = static fn(string $fqcn): string => (new ReflectionClass($fqcn))->getFileName();

return static function (ContainerConfigurator $configurator) use ($classToFileName): void {
    $services = $configurator->services()
        ->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load(namespace: 'MarsRoverKata\\', resource: '../src/*')
        ->exclude(
            [
                '../src/Domain',
                '../src/Application/Command',
                '../src/Application/Query',
                $classToFileName(Kernel::class)
            ]
        );

    $services->alias(
        id: CommandBus::class,
        referencedId: \MarsRoverKata\Infrastructure\Symfony\CommandBus::class
    );

    $services->load(
        namespace: 'MarsRoverKata\Application\Command\\',
        resource: '../src/Application/Command/**/**Handler.php'
    )->tag('messenger.message_handler', ['bus' => 'command_bus']);

    $services->load(
        namespace: 'MarsRoverKata\Application\Query\\',
        resource: '../src/Application/Query/**/**Service.php'
    )->public()
        ->autowire()
        ->autoconfigure();

    $services->load(
        namespace: 'MarsRoverKata\Application\Query\\',
        resource: '../src/Application/Query/**/**Repository.php'
    );

    $services->alias(\Broadway\EventStore\EventStore::class, 'broadway.event_store');
    $services->alias(\Broadway\EventHandling\EventBus::class, 'broadway.event_handling.event_bus');
    $services->alias(\MarsRoverKata\Domain\MarsRover\MarsRoverRepository::class, \MarsRoverKata\Infrastructure\Broadway\MarsRover\MarsRoverRepositoryImpl::class);
};