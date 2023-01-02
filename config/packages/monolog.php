<?php

declare(strict_types=1);

use Symfony\Config\MonologConfig;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (MonologConfig $monologConfig, ContainerConfigurator $containerConfigurator): void {
    if ('dev' === $containerConfigurator->env()) {
        $containerConfigurator->extension('monolog', [
            'handlers' => [
                'main' => [
                    'type' => 'stream',
                    'path' => '%kernel.logs_dir%/%kernel.environment%.log',
                    'level' => 'debug',
                    'channels' => ['!event'],
                ],
                'console' => [
                    'type' => 'console',
                    'process_psr_3_messages' => false,
                    'channels' => ['!event', '!doctrine', '!console'],
                ],
            ],
        ]);
    }

    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('monolog', [
            'handlers' => [
                'main' => [
                    'type' => 'fingers_crossed',
                    'action_level' => 'error',
                    'handler' => 'nested',
                    'excluded_http_codes' => [404, 405],
                    'channels' => ['!event'],
                ],
                'nested' => [
                    'type' => 'stream',
                    'path' => '%kernel.logs_dir%/%kernel.environment%.log',
                    'level' => 'debug',
                ],
            ],
        ]);
    }

    if ('prod' === $containerConfigurator->env()) {
        $containerConfigurator->extension('monolog', [
            'handlers' => [
                'main' => [
                    'type' => 'fingers_crossed',
                    'action_level' => 'critical',
                    'handler' => 'nested',
                    'excluded_http_codes' => [404, 405],
                    'buffer_size' => 50,
                ],
                'grouped' => [
                    'type' => 'group',
                    'members' => ['nested'],
                ],
                'nested' => [
                    'type' => 'stream',
                    'path' => 'php://stderr',
                    'level' => 'debug',
                    'formatter' => 'monolog.formatter.json',
                ],
                'console' => [
                    'type' => 'console',
                    'process_psr_3_messages' => false,
                    'channels' => ['!event', '!doctrine'],
                ],
                'deduplicated' => [
                    'type' => 'deduplication',
                    'handler' => 'symfony_mailer',
                ],
                'symfony_mailer' => [
                    'type' => 'symfony_mailer',
                    'from_email' => '%app.mailer.sender%',
                    'to_email' => '%app.mailer.admin%',
                    'subject' => '[%app.mailer.from%]: An Error Occured! %%message%%',
                    'level' => 'debug',
                    'formatter' => 'monolog.formatter.html',
                    'content_type' => 'text/html',
                ],
            ],
        ]);
    }
};
