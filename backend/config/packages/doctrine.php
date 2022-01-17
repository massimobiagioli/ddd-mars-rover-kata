<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension(
        namespace: 'doctrine',
        config: [
            'dbal' => [
                'override_url' => true,
                'url' => '%env(resolve:DATABASE_URL)%',
            ],
            'orm' => [
                'auto_generate_proxy_classes' => true,
                'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
                'auto_mapping' => true,
                'mappings' => [
                    'Domain' => [
                        'is_bundle' => false,
                        'type' => 'annotation',
                        'dir' => '%kernel.project_dir%/src/Domain',
                        'prefix' => 'MarsRoverKata\\Domain',
                        'alias' => 'Domain',
                    ],
                    'ReadModel' => [
                        'is_bundle' => false,
                        'type' => 'annotation',
                        'dir' => '%kernel.project_dir%/src/Application/Query',
                        'prefix' => 'MarsRoverKata\\Application\\Query',
                        'alias' => 'ReadModel',
                    ],
                ]
            ],
        ],
    );
};