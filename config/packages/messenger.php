<?php

declare(strict_types=1);

use App\Application\Handler\Import\ImportDataHandler;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'messenger' => [
            'failure_transport' => 'failed',
            'transports' => [
                'async' => '%env(MESSENGER_TRANSPORT_DSN)%',
                'failed' => 'doctrine://default?queue_name=failed',
                'sync' => 'sync://',
            ],
            'routing' => [
                ImportDataHandler::class => 'async',
            ],
        ],
    ]);

    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('framework', [
            'messenger' => [
                'transports' => [
                    'async' => 'in-memory://',
                ],
            ],
        ]);
    }
};
