<?php

declare(strict_types=1);

use DoctrineExtensions\Query\Mysql\DateFormat;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $doctrineConfig, ContainerConfigurator $containerConfigurator): void {
    $doctrineConfig->dbal()->connection('default')->url('%env(resolve:DATABASE_URL)%');

    $emDefault = $doctrineConfig->orm()->entityManager('default');

    $emDefault->namingStrategy('doctrine.orm.naming_strategy.underscore_number_aware');
    $emDefault->autoMapping(true);

    $emDefault->mapping('App')
        ->isBundle(false)
        ->type('attribute')
        ->dir('%kernel.project_dir%/src/Entity')
        ->prefix('App\Entity')
        ->alias('App');

    $emDefault->dql([
        'datetime_functions' => [
            'DATE_FORMAT' => DateFormat::class,
        ],
    ]);
};
