<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\MonologConfig;

return static function (MonologConfig $monolog, ContainerConfigurator $containerConfigurator): void {
    if ('dev' === $containerConfigurator->env()) {
        $monolog->handler('main')
            ->type('stream')
            ->path('%kernel.logs_dir%/%kernel.environment%.log')
            ->level('debug')
            ->channels()->elements(['!event'])
        ;

        $monolog->handler('console')
            ->type('console')
            ->processPsr3Messages(false)
            ->channels()->elements(['!event', '!doctrine', '!console'])
        ;
    }

    if ('prod' === $containerConfigurator->env()) {
        $monolog->handler('main')
            ->type('fingers_crossed')
            ->actionLevel('critical')
            ->handler('grouped')
            ->channels()->elements(['!mailer'])
        ;

        $monolog->handler('grouped')
            ->type('group')
            ->members(['streamed', 'deduplicated'])
        ;

        $monolog->handler('deduplicated')
            ->type('deduplication')
            ->handler('symfony_mailer');

        $monolog->handler('symfony_mailer')
            ->type('symfony_mailer')
            ->fromEmail('%app.mailer.from_address%')
            ->toEmail(['%app.mailer.admin%'])
            ->subject('[%app.mailer.from_sender%]: %%message%%')
            ->level('debug')
            ->formatter('monolog.formatter.html')
            ->contentType('text/html')
        ;

        $monolog->handler('streamed')
            ->type('stream')
            ->path('%kernel.logs_dir%/%kernel.environment%.log')
            ->level('critical')
        ;

        $monolog->handler('console')
            ->type('console')
            ->processPsr3Messages(false)
            ->channels()->elements(['!event', '!doctrine', '!console'])
        ;
    }

    if ('test' === $containerConfigurator->env()) {
        $monolog->handler('main')
            ->type('fingers_crossed')
            ->actionLevel('error')
            ->handler('nested')
            ->channels()->elements(['!event'])
        ;

        $monolog->handler('nested')
            ->type('stream')
            ->path('%kernel.logs_dir%/%kernel.environment%.log')
            ->level('debug')
        ;
    }
};
