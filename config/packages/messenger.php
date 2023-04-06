<?php

declare(strict_types=1);

use App\Domain\Command\Export\ExportTracerByQuarterCommand;
use App\Domain\Command\Import\ImportDataCommand;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework, ContainerConfigurator $containerConfigurator): void {
    $messenger = $framework->messenger();

    $messenger->transport('async')->dsn('%env(MESSENGER_TRANSPORT_DSN)%');
    $messenger->transport('sync')->dsn('sync://');

    $messenger->transport('failed')->dsn('doctrine://default?queue_name=failed');
    $messenger->failureTransport('failed');

    $framework->messenger()->routing(ExportTracerByQuarterCommand::class)->senders(['async']);
    $framework->messenger()->routing(ImportDataCommand::class)->senders(['async']);

    if ('test' === $containerConfigurator->env()) {
        $messenger->transport('async')->dsn('in-memory://');
    }
};
