<?php
declare(strict_types=1);

use MarsRoverKata\Kernel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

$classToFileName = static fn(string $fqcn): string => (new ReflectionClass($fqcn))->getFileName();

return static function (ContainerConfigurator $configurator) use ($classToFileName): void {
    $services = $configurator->services()
        ->defaults()
        ->public()
        ->autowire()
        ->autoconfigure()
    ;
};