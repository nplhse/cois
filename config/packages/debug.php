<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\DebugConfig;

return static function (DebugConfig $debugConfig, ContainerConfigurator $containerConfigurator): void {
    if ('dev' === $containerConfigurator->env()) {
        $debugConfig->dumpDestination('tcp://%env(VAR_DUMPER_SERVER)%');
    }
};
