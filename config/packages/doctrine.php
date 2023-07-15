<?php

declare(strict_types=1);

use DoctrineExtensions\Query\Mysql\DateFormat;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'url' => '%env(resolve:DATABASE_URL)%',
        ],
        'orm' => [
            'auto_generate_proxy_classes' => true,
            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
            'auto_mapping' => true,
            'mappings' => [
                'App' => [
                    'is_bundle' => false,
                    'dir' => '%kernel.project_dir%/src/App/Entity',
                    'prefix' => 'App\Entity',
                    'alias' => 'App',
                ],
            ],
            'dql' => [
                'datetime_functions' => [
                    'DATE_FORMAT' => DateFormat::class,
                    'year' => DoctrineExtensions\Query\Mysql\Year::class,
                    'quarter' => DoctrineExtensions\Query\Mysql\Quarter::class,
                    'month' => DoctrineExtensions\Query\Mysql\Month::class,
                    'hour' => DoctrineExtensions\Query\Mysql\Hour::class,
                ],
            ],
        ],
    ]);

    if ('prod' === $containerConfigurator->env()) {
        $containerConfigurator->extension('doctrine', [
            'orm' => [
                'auto_generate_proxy_classes' => false,
                'metadata_cache_driver' => [
                    'type' => 'pool',
                    'pool' => 'doctrine.system_cache_pool',
                ],
                'query_cache_driver' => [
                    'type' => 'pool',
                    'pool' => 'doctrine.system_cache_pool',
                ],
                'result_cache_driver' => [
                    'type' => 'pool',
                    'pool' => 'doctrine.result_cache_pool',
                ],
            ],
        ]);

        $containerConfigurator->extension('framework', [
            'cache' => [
                'pools' => [
                    'doctrine.result_cache_pool' => [
                        'adapter' => 'cache.app',
                    ],
                    'doctrine.system_cache_pool' => [
                        'adapter' => 'cache.system',
                    ],
                ],
            ],
        ]);
    }

    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('doctrine', [
            'dbal' => [
                'dbname_suffix' => '_test%env(default::TEST_TOKEN)%',
            ],
        ]);
    }
};
