<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('monolog', [
        'channels' => ['deprecation'],
        'handlers' => [
            'deprecation' => [
                'type' => 'stream',
                'channels' => ['deprecation'],
                'path' => '%kernel.logs_dir%/%kernel.environment%.deprecations.log',
            ],
        ],
    ]);
};
