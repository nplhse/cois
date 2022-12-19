<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('monolog', [
        'handlers' => [
            'main' => [
                'type' => 'fingers_crossed',
                'action_level' => 'critical',
                'excluded_http_codes' => [404, 405],
                'buffer_size' => 50,
                'handler' => 'grouped',
            ],
            'grouped' => [
                'type' => 'group',
                'members' => ['nested', 'deduplicated'],
            ],
            'nested' => [
                'type' => 'stream',
                'path' => 'php://stderr',
                'level' => 'debug',
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
};